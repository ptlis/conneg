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
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

abstract class NegotiationDataProvider extends \PHPUnit_Framework_TestCase
{
    public function charsetProvider()
    {
        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'utf-8,iso-8859-5;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('utf-8', 1.0, Type::EXACT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('utf-8', 1.0, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('iso-8859-5', 0.75, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'iso-8859-1;q=1,utf-8;q=0.5',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('iso-8859-1', 1.0, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('iso-8859-1', 1.0, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('utf-8', 0.5, Type::EXACT_TYPE)
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
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('iso-8859-1', 0.5, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('iso-8859-1', 0.5, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('utf-8', 0.5, Type::EXACT_TYPE)
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
                    new Type('utf-8', 0.3, Type::EXACT_TYPE),
                    new Type('utf-8', 0.6, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('utf-8', 0.3, Type::EXACT_TYPE),
                            new Type('utf-8', 0.6, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('iso-8859-1', 0.5, Type::EXACT_TYPE),
                            new Type('iso-8859-1', 0.3, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('windows-1250', 0.8, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('iso-8859-5', 0.9, Type::EXACT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'iso-8859-5;q=0.3,utf-8;q=0.9,*;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new TypePair(
                    new Type('*', 0.5, Type::WILDCARD_TYPE),
                    new Type('windows-1250', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('windows-1250', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('iso-8859-5', 0.3, Type::EXACT_TYPE),
                            new Type('iso-8859-5', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('utf-8', 0.9, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,iso-8859-5;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new TypePair(
                    new Type('iso-8859-5', 0.5, Type::EXACT_TYPE),
                    new Type('iso-8859-5', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('iso-8859-5', 0.5, Type::EXACT_TYPE),
                            new Type('iso-8859-5', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('windows-1250', 1, Type::EXACT_TYPE)
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
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => '7zip,gzip;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('7zip', 1.0, Type::EXACT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', 1.0, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('gzip', 0.75, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'compress;q=1,7zip;q=0.5',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('compress', 1.0, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('compress', 1.0, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('7zip', 0.5, Type::EXACT_TYPE)
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
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('7zip', 0.5, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('7zip', 0.5, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('compress', 0.5, Type::EXACT_TYPE)
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
                    new Type('7zip', 0.3, Type::EXACT_TYPE),
                    new Type('7zip', 0.6, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', 0.3, Type::EXACT_TYPE),
                            new Type('7zip', 0.6, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('deflate', 0.5, Type::EXACT_TYPE),
                            new Type('deflate', 0.3, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('compress', 0.8, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('x-propriatary', 0.9, Type::EXACT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'compress;q=0.3,7zip;q=0.9,*;q=0.5',
                'app' => 'compress,deflate',
                'best' => new TypePair(
                    new Type('*', 0.5, Type::WILDCARD_TYPE),
                    new Type('deflate', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('deflate', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('compress', 0.3, Type::EXACT_TYPE),
                            new Type('compress', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('7zip', 0.9, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,7zip;q=0.5',
                'app' => '7zip,compress',
                'best' => new TypePair(
                    new Type('7zip', 0.5, Type::EXACT_TYPE),
                    new Type('7zip', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('7zip', 0.5, Type::EXACT_TYPE),
                            new Type('7zip', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('compress', 1, Type::EXACT_TYPE)
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
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'en-GB,es;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new Type('en-GB', 1.0, Type::EXACT_TYPE),
                    new Type('', 0, Type::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', 1.0, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('es', 0.75, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'de;q=1,fr;q=0.5',
                'best' => new TypePair(
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('de', 1.0, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('de', 1.0, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('fr', 0.5, Type::EXACT_TYPE)
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
                    new Type('', 0, Type::ABSENT_TYPE),
                    new Type('af', 0.5, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('af', 0.5, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('bg', 0.5, Type::EXACT_TYPE)
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
                    new Type('en-GB', 0.3, Type::EXACT_TYPE),
                    new Type('en-GB', 0.6, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', 0.3, Type::EXACT_TYPE),
                            new Type('en-GB', 0.6, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('de', 0.5, Type::EXACT_TYPE),
                            new Type('de', 0.3, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('en', 0.8, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new Type('', 0, Type::ABSENT_TYPE),
                            new Type('cs', 0.9, Type::EXACT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'en-GB;q=0.3,de;q=0.9,*;q=0.5',
                'app' => 'en-GB,fr',
                'best' => new TypePair(
                    new Type('*', 0.5, Type::WILDCARD_TYPE),
                    new Type('fr', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('fr', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('en-GB', 0.3, Type::EXACT_TYPE),
                            new Type('en-GB', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('de', 0.9, Type::EXACT_TYPE),
                            new Type('', 0, Type::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,en-GB;q=0.5',
                'app' => 'en-GB,en-US',
                'best' => new TypePair(
                    new Type('en-GB', 0.5, Type::EXACT_TYPE),
                    new Type('en-GB', 1, Type::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new Type('en-GB', 0.5, Type::EXACT_TYPE),
                            new Type('en-GB', 1, Type::EXACT_TYPE)
                        ),
                        new TypePair(
                            new Type('*', 0.5, Type::WILDCARD_TYPE),
                            new Type('en-US', 1, Type::EXACT_TYPE)
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
                new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new TypePair(
                    new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                    new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'text/html,application/xml;q=0.75',
                'app' => '',
                'best' => new TypePair(
                    new MimeType('text', 'html', 1.0, MimeType::EXACT_TYPE),
                    new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', 1.0, MimeType::EXACT_TYPE),
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', 0.75, MimeType::EXACT_TYPE),
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => 'application/rdf+xml;q=1,text/n3;q=0.5',
                'app' => '',
                'best' => new TypePair(
                    new MimeType('application', 'rdf+xml', 1, MimeType::EXACT_TYPE),
                    new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('application', 'rdf+xml', 1, MimeType::EXACT_TYPE),
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('text', 'n3', 0.5, MimeType::EXACT_TYPE),
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE)
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
                    new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                    new MimeType('text', 'html', 0.5, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                            new MimeType('text', 'html', 0.5, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                            new MimeType('text', 'n3', 0.5, MimeType::EXACT_TYPE)
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
                    new MimeType('application', 'json', 0.3, MimeType::EXACT_TYPE),
                    new MimeType('application', 'json', 0.6, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('application', 'json', 0.3, MimeType::EXACT_TYPE),
                            new MimeType('application', 'json', 0.6, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('text', 'html', 0.5, MimeType::EXACT_TYPE),
                            new MimeType('text', 'html', 0.3, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', 0.8, MimeType::EXACT_TYPE),
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                            new MimeType('text', 'n3', 0.9, MimeType::EXACT_TYPE)
                        )
                    )
                )
            ),

            // Test subtype wildcard matching
            'subtype_wildcard' => array(
                'user' => 'text/*;q=0.8,application/xml;q=0.9',
                'app' => 'text/html,application/xml;q=0.7,text/n3;q=0.3',
                'best' => new TypePair(
                    new MimeType('text', '*', 0.8, MimeType::WILDCARD_SUBTYPE),
                    new MimeType('text', 'html', 1, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', '*', 0.8, MimeType::WILDCARD_SUBTYPE),
                            new MimeType('text', 'html', 1, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('application', 'xml', 0.9, MimeType::EXACT_TYPE),
                            new MimeType('application', 'xml', 0.7, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('text', '*', 0.8, MimeType::WILDCARD_SUBTYPE),
                            new MimeType('text', 'n3', 0.3, MimeType::EXACT_TYPE)
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
                    new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                    new MimeType('text', 'html', 0.75, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                            new MimeType('text', 'html', 0.75, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('text', '*', 0.75, MimeType::WILDCARD_SUBTYPE),
                            new MimeType('text', 'plain', 1, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                            new MimeType('application', 'xml', 0.9, MimeType::EXACT_TYPE)
                        )
                    )
                )
            ),

            // Test full wildcard matching
            'full_wildcard' => array(
                'user' => '*/*;q=0.75,text/html',
                'app' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new TypePair(
                    new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                    new MimeType('text', 'html', 0.75, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                            new MimeType('text', 'html', 0.75, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('*', '*', 0.75, MimeType::WILDCARD_TYPE),
                            new MimeType('text', 'plain', 1, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('*', '*', 0.75, MimeType::WILDCARD_TYPE),
                            new MimeType('application', 'xml', 0.9, MimeType::EXACT_TYPE)
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
                    new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                    new MimeType('text', 'html', 1, MimeType::EXACT_TYPE)
                ),
                'all' => new TypePairCollection(
                    $sort,
                    array(
                        new TypePair(
                            new MimeType('text', 'html', 1, MimeType::EXACT_TYPE),
                            new MimeType('text', 'html', 1, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('text', '*', 1, MimeType::WILDCARD_SUBTYPE),
                            new MimeType('text', 'plain', 1, MimeType::EXACT_TYPE)
                        ),
                        new TypePair(
                            new MimeType('*', '*', 1, MimeType::WILDCARD_TYPE),
                            new MimeType('application', 'xml', 1, MimeType::EXACT_TYPE)
                        )
                    )
                )
            ),

            // TODO: Test with presence of accept-extens components
        );
    }
}
