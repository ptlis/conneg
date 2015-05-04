<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

abstract class AbstractNegotiationTest extends \PHPUnit_Framework_TestCase
{
    public function charsetProvider()
    {
        $sort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new AbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'utf-8,iso-8859-5;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('utf-8', new QualityFactor(1.0)),
                    new AbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('utf-8', new QualityFactor(1.0)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new Type('iso-8859-5', new QualityFactor(0.75)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'iso-8859-1;q=1,utf-8;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('iso-8859-1', new QualityFactor(1.0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('iso-8859-1', new QualityFactor(1.0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('utf-8', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'utf-8;q=0.5,iso-8859-1;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('iso-8859-1', new QualityFactor(0.5))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('iso-8859-1', new QualityFactor(0.5))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('utf-8', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'windows-1250;q=0.8,utf-8;q=0.3,iso-8859-1;q=0.5',
                'app' => 'utf-8;q=0.6,iso-8859-5;q=0.9,iso-8859-1;q=0.3',
                'best' => new TypePair(
                    new Type('utf-8', new QualityFactor(0.3)),
                    new Type('utf-8', new QualityFactor(0.6))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('utf-8', new QualityFactor(0.3)),
                            new Type('utf-8', new QualityFactor(0.6))
                        ),
                        new TypePair(
                            new Type('iso-8859-1', new QualityFactor(0.5)),
                            new Type('iso-8859-1', new QualityFactor(0.3))
                        ),
                        new TypePair(
                            new Type('windows-1250', new QualityFactor(0.8)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('iso-8859-5', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'iso-8859-5;q=0.3,utf-8;q=0.9,*;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new TypePair(
                    new WildcardType(new QualityFactor(0.5)),
                    new Type('windows-1250', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('windows-1250', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('iso-8859-5', new QualityFactor(0.3)),
                            new Type('iso-8859-5', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('utf-8', new QualityFactor(0.9)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,iso-8859-5;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new TypePair(
                    new Type('iso-8859-5', new QualityFactor(0.5)),
                    new Type('iso-8859-5', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('iso-8859-5', new QualityFactor(0.5)),
                            new Type('iso-8859-5', new QualityFactor(1))
                        ),
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('windows-1250', new QualityFactor(1))
                        )
                    )
                )
            )
        );
    }
}
