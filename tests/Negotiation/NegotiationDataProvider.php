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
use ptlis\ConNeg\Type\MimeAbsentType;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\MimeWildcardSubType;
use ptlis\ConNeg\Type\MimeWildcardType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

abstract class NegotiationDataProvider extends \PHPUnit_Framework_TestCase
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

    public function encodingProvider()
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
                'user' => '7zip,gzip;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('7zip', new QualityFactor(1.0)),
                    new AbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', new QualityFactor(1.0)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new Type('gzip', new QualityFactor(0.75)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'compress;q=1,7zip;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('compress', new QualityFactor(1.0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('compress', new QualityFactor(1.0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('7zip', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'compress;q=0.5, 7zip;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('7zip', new QualityFactor(0.5))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('7zip', new QualityFactor(0.5))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('compress', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'compress;q=0.8,7zip;q=0.3,deflate;q=0.5',
                'app' => '7zip;q=0.6,x-propriatary;q=0.9,deflate;q=0.3',
                'best' => new TypePair(
                    new Type('7zip', new QualityFactor(0.3)),
                    new Type('7zip', new QualityFactor(0.6))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', new QualityFactor(0.3)),
                            new Type('7zip', new QualityFactor(0.6))
                        ),
                        new TypePair(
                            new Type('deflate', new QualityFactor(0.5)),
                            new Type('deflate', new QualityFactor(0.3))
                        ),
                        new TypePair(
                            new Type('compress', new QualityFactor(0.8)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('x-propriatary', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'compress;q=0.3,7zip;q=0.9,*;q=0.5',
                'app' => 'compress,deflate',
                'best' => new TypePair(
                    new WildcardType(new QualityFactor(0.5)),
                    new Type('deflate', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('deflate', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('compress', new QualityFactor(0.3)),
                            new Type('compress', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('7zip', new QualityFactor(0.9)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,7zip;q=0.5',
                'app' => '7zip,compress',
                'best' => new TypePair(
                    new Type('7zip', new QualityFactor(0.5)),
                    new Type('7zip', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', new QualityFactor(0.5)),
                            new Type('7zip', new QualityFactor(1))
                        ),
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('compress', new QualityFactor(1))
                        )
                    )
                )
            )
        );
    }

    public function languageProvider()
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
                'user' => 'en-GB,es;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('en-GB', new QualityFactor(1.0)),
                    new AbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', new QualityFactor(1.0)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new Type('es', new QualityFactor(0.75)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'de;q=1,fr;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('de', new QualityFactor(1.0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('de', new QualityFactor(1.0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('fr', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'af;q=0.5, bg;q=0.5',
                'best' => new TypePair(
                    new AbsentType(new QualityFactor(0)),
                    new Type('af', new QualityFactor(0.5))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('af', new QualityFactor(0.5))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('bg', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'en;q=0.8,en-GB;q=0.3,de;q=0.5',
                'app' => 'en-GB;q=0.6,cs;q=0.9,de;q=0.3',
                'best' => new TypePair(
                    new Type('en-GB', new QualityFactor(0.3)),
                    new Type('en-GB', new QualityFactor(0.6))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', new QualityFactor(0.3)),
                            new Type('en-GB', new QualityFactor(0.6))
                        ),
                        new TypePair(
                            new Type('de', new QualityFactor(0.5)),
                            new Type('de', new QualityFactor(0.3))
                        ),
                        new TypePair(
                            new Type('en', new QualityFactor(0.8)),
                            new AbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new AbsentType(new QualityFactor(0)),
                            new Type('cs', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'en-GB;q=0.3,de;q=0.9,*;q=0.5',
                'app' => 'en-GB,fr',
                'best' => new TypePair(
                    new WildcardType(new QualityFactor(0.5)),
                    new Type('fr', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('fr', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('en-GB', new QualityFactor(0.3)),
                            new Type('en-GB', new QualityFactor(1))
                        ),
                        new TypePair(
                            new Type('de', new QualityFactor(0.9)),
                            new AbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,en-GB;q=0.5',
                'app' => 'en-GB,en-US',
                'best' => new TypePair(
                    new Type('en-GB', new QualityFactor(0.5)),
                    new Type('en-GB', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', new QualityFactor(0.5)),
                            new Type('en-GB', new QualityFactor(1))
                        ),
                        new TypePair(
                            new WildcardType(new QualityFactor(0.5)),
                            new Type('en-US', new QualityFactor(1))
                        )
                    )
                )
            )
        );
    }


    public function mimeProvider()
    {
        $sort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new MimeAbsentType(new QualityFactor(0)),
                    new MimeAbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'text/html,application/xml;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new MimeType('text', 'html', new QualityFactor(1.0)),
                    new MimeAbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', new QualityFactor(1.0)),
                            new MimeAbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', new QualityFactor(0.75)),
                            new MimeAbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => 'application/rdf+xml;q=1,text/n3;q=0.5',
                'app' => '',
                'best' => new TypePair(
                    new MimeType('application', 'rdf+xml', new QualityFactor(1)),
                    new MimeAbsentType(new QualityFactor(0))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('application', 'rdf+xml', new QualityFactor(1)),
                            new MimeAbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new MimeType('text', 'n3', new QualityFactor(0.5)),
                            new MimeAbsentType(new QualityFactor(0))
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'text/n3;q=0.5,text/html;q=0.5',
                'best' => new TypePair(
                    new MimeAbsentType(new QualityFactor(0)),
                    new MimeType('text', 'html', new QualityFactor(0.5))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeAbsentType(new QualityFactor(0)),
                            new MimeType('text', 'html', new QualityFactor(0.5))
                        ),
                        new TypePair(
                            new MimeAbsentType(new QualityFactor(0)),
                            new MimeType('text', 'n3', new QualityFactor(0.5))
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'application/xml;q=0.8,application/json;q=0.3,text/html;q=0.5',
                'app' => 'application/json;q=0.6,text/n3;q=0.9,text/html;q=0.3',
                'best' => new TypePair(
                    new MimeType('application', 'json', new QualityFactor(0.3)),
                    new MimeType('application', 'json', new QualityFactor(0.6))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('application', 'json', new QualityFactor(0.3)),
                            new MimeType('application', 'json', new QualityFactor(0.6))
                        ),
                        new TypePair(
                            new MimeType('text', 'html', new QualityFactor(0.5)),
                            new MimeType('text', 'html', new QualityFactor(0.3))
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', new QualityFactor(0.8)),
                            new MimeAbsentType(new QualityFactor(0))
                        ),
                        new TypePair(
                            new MimeAbsentType(new QualityFactor(0)),
                            new MimeType('text', 'n3', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test subtype wildcard matching
            'subtype_wildcard' => array(
                'user' => 'text/*;q=0.8,application/xml;q=0.9',
                'app' => 'text/html,application/xml;q=0.7,text/n3;q=0.3',
                'best' => new TypePair(
                    new MimeWildcardSubType('text', new QualityFactor(0.8)),
                    new MimeType('text', 'html', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeWildcardSubType('text', new QualityFactor(0.8)),
                            new MimeType('text', 'html', new QualityFactor(1))
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', new QualityFactor(0.9)),
                            new MimeType('application', 'xml', new QualityFactor(0.7))
                        ),
                        new TypePair(
                            new MimeWildcardSubType('text', new QualityFactor(0.8)),
                            new MimeType('text', 'n3', new QualityFactor(0.3))
                        )
                    )
                )
            ),

            // Test subtype wildcard precedence
            // If a wildcard and exact match have the came quality factor product the exact match is preferred
            'subtype_wildcard_precedence' => array(
                'user' => 'text/*;q=0.75,text/html',
                'app' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new TypePair(
                    new MimeType('text', 'html', new QualityFactor(1)),
                    new MimeType('text', 'html', new QualityFactor(0.75))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', new QualityFactor(1)),
                            new MimeType('text', 'html', new QualityFactor(0.75))
                        ),
                        new TypePair(
                            new MimeWildcardSubType('text', new QualityFactor(0.75)),
                            new MimeType('text', 'plain', new QualityFactor(1))
                        ),
                        new TypePair(
                            new MimeAbsentType(new QualityFactor(0)),
                            new MimeType('application', 'xml', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test full wildcard matching
            'full_wildcard' => array(
                'user' => '*/*;q=0.75,text/html',
                'app' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new TypePair(
                    new MimeType('text', 'html', new QualityFactor(1)),
                    new MimeType('text', 'html', new QualityFactor(0.75))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', new QualityFactor(1)),
                            new MimeType('text', 'html', new QualityFactor(0.75))
                        ),
                        new TypePair(
                            new MimeWildcardType(new QualityFactor(0.75)),
                            new MimeType('text', 'plain', new QualityFactor(1))
                        ),
                        new TypePair(
                            new MimeWildcardType(new QualityFactor(0.75)),
                            new MimeType('application', 'xml', new QualityFactor(0.9))
                        )
                    )
                )
            ),

            // Test full wildcard precedence
            // The full match has higher precedence, followed by partial wildcard match (e.g. text/*) and a full
            // wildcard match has the lowest precedence.
            'full_wildcard_precedence' => array(
                'user' => '*/*,text/*,text/html',
                'app' => 'text/plain,text/html,application/xml',
                'best' => new TypePair(
                    new MimeType('text', 'html', new QualityFactor(1)),
                    new MimeType('text', 'html', new QualityFactor(1))
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', new QualityFactor(1)),
                            new MimeType('text', 'html', new QualityFactor(1))
                        ),
                        new TypePair(
                            new MimeWildcardSubType('text', new QualityFactor(1)),
                            new MimeType('text', 'plain', new QualityFactor(1))
                        ),
                        new TypePair(
                            new MimeWildcardType(new QualityFactor(1)),
                            new MimeType('application', 'xml', new QualityFactor(1))
                        )
                    )
                )
            ),

            // TODO: Test with presence of accept-extens components
        );
    }
}
