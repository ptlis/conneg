<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;

abstract class NegotiationDataProvider extends \PHPUnit_Framework_TestCase
{
    public function charsetProvider()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference('', 0, Preference::ABSENT_TYPE),
                new Preference('', 0, Preference::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'utf-8,iso-8859-5;q=0.75',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('utf-8', 1.0, Preference::COMPLETE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('utf-8', 1.0, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('iso-8859-5', 0.75, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'iso-8859-1;q=1,utf-8;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('iso-8859-1', 1.0, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('iso-8859-1', 1.0, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('utf-8', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'utf-8;q=0.5,iso-8859-1;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('iso-8859-1', 0.5, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('iso-8859-1', 0.5, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('utf-8', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'windows-1250;q=0.8,utf-8;q=0.3,iso-8859-1;q=0.5',
                'app' => 'utf-8;q=0.6,iso-8859-5;q=0.9,iso-8859-1;q=0.3',
                'best' => new MatchedPreferences(
                    new Preference('utf-8', 0.3, Preference::COMPLETE),
                    new Preference('utf-8', 0.6, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('utf-8', 0.3, Preference::COMPLETE),
                            new Preference('utf-8', 0.6, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('iso-8859-1', 0.5, Preference::COMPLETE),
                            new Preference('iso-8859-1', 0.3, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('windows-1250', 0.8, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('iso-8859-5', 0.9, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'iso-8859-5;q=0.3,utf-8;q=0.9,*;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new MatchedPreferences(
                    new Preference('*', 0.5, Preference::WILDCARD),
                    new Preference('windows-1250', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('windows-1250', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('iso-8859-5', 0.3, Preference::COMPLETE),
                            new Preference('iso-8859-5', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('utf-8', 0.9, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,iso-8859-5;q=0.5',
                'app' => 'iso-8859-5,windows-1250',
                'best' => new MatchedPreferences(
                    new Preference('iso-8859-5', 0.5, Preference::COMPLETE),
                    new Preference('iso-8859-5', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('iso-8859-5', 0.5, Preference::COMPLETE),
                            new Preference('iso-8859-5', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('windows-1250', 1, Preference::COMPLETE)
                        )
                    )
                )
            )
        );
    }

    public function encodingProvider()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference('', 0, Preference::ABSENT_TYPE),
                new Preference('', 0, Preference::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => '7zip,gzip;q=0.75',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('7zip', 1.0, Preference::COMPLETE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('7zip', 1.0, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('gzip', 0.75, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'compress;q=1,7zip;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('compress', 1.0, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('compress', 1.0, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('7zip', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'compress;q=0.5, 7zip;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('7zip', 0.5, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('7zip', 0.5, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('compress', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'compress;q=0.8,7zip;q=0.3,deflate;q=0.5',
                'app' => '7zip;q=0.6,x-propriatary;q=0.9,deflate;q=0.3',
                'best' => new MatchedPreferences(
                    new Preference('7zip', 0.3, Preference::COMPLETE),
                    new Preference('7zip', 0.6, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('7zip', 0.3, Preference::COMPLETE),
                            new Preference('7zip', 0.6, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('deflate', 0.5, Preference::COMPLETE),
                            new Preference('deflate', 0.3, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('compress', 0.8, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('x-propriatary', 0.9, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'compress;q=0.3,7zip;q=0.9,*;q=0.5',
                'app' => 'compress,deflate',
                'best' => new MatchedPreferences(
                    new Preference('*', 0.5, Preference::WILDCARD),
                    new Preference('deflate', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('deflate', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('compress', 0.3, Preference::COMPLETE),
                            new Preference('compress', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('7zip', 0.9, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,7zip;q=0.5',
                'app' => '7zip,compress',
                'best' => new MatchedPreferences(
                    new Preference('7zip', 0.5, Preference::COMPLETE),
                    new Preference('7zip', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('7zip', 0.5, Preference::COMPLETE),
                            new Preference('7zip', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('compress', 1, Preference::COMPLETE)
                        )
                    )
                )
            )
        );
    }

    public function languageProvider()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference('', 0, Preference::ABSENT_TYPE),
                new Preference('', 0, Preference::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'en-GB,es;q=0.75',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('en-GB', 1.0, Preference::COMPLETE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('en-GB', 1.0, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('es', 0.75, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => '',
                'app' => 'de;q=1,fr;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('de', 1.0, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('de', 1.0, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('fr', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'af;q=0.5, bg;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('af', 0.5, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('af', 0.5, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('bg', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'en;q=0.8,en-GB;q=0.3,de;q=0.5',
                'app' => 'en-GB;q=0.6,cs;q=0.9,de;q=0.3',
                'best' => new MatchedPreferences(
                    new Preference('en-GB', 0.3, Preference::COMPLETE),
                    new Preference('en-GB', 0.6, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('en-GB', 0.3, Preference::COMPLETE),
                            new Preference('en-GB', 0.6, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('de', 0.5, Preference::COMPLETE),
                            new Preference('de', 0.3, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('en', 0.8, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('cs', 0.9, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'user' => 'en-GB;q=0.3,de;q=0.9,*;q=0.5',
                'app' => 'en-GB,fr',
                'best' => new MatchedPreferences(
                    new Preference('*', 0.5, Preference::WILDCARD),
                    new Preference('fr', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('fr', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('en-GB', 0.3, Preference::COMPLETE),
                            new Preference('en-GB', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('de', 0.9, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'user' => '*;q=0.5,en-GB;q=0.5',
                'app' => 'en-GB,en-US',
                'best' => new MatchedPreferences(
                    new Preference('en-GB', 0.5, Preference::COMPLETE),
                    new Preference('en-GB', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('en-GB', 0.5, Preference::COMPLETE),
                            new Preference('en-GB', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*', 0.5, Preference::WILDCARD),
                            new Preference('en-US', 1, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test matching of partial language wildcards to languages
            'test_partial_language' => array(
                'user' => 'en-GB,es',
                'app' => 'es-*;q=0.75,es-ES,es-419',
                'best' => new MatchedPreferences(
                    new Preference('es', 1, Preference::COMPLETE),
                    new Preference('es-*', 0.75, Preference::PARTIAL_WILDCARD)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('es', 1, Preference::COMPLETE),
                            new Preference('es-*', 0.75, Preference::PARTIAL_WILDCARD)
                        ),
                        new MatchedPreferences(
                            new Preference('en-GB', 1, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('es-419', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('es-ES', 1, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test matching of partial language wildcards matching subtypes
            'test_partial_language_subtype' => array(
                'user' => 'es-CO,es-ES',
                'app' => 'es-*;q=0.75,es-ES,es-419',
                'best' => new MatchedPreferences(
                    new Preference('es-ES', 1, Preference::COMPLETE),
                    new Preference('es-ES', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('es-ES', 1, Preference::COMPLETE),
                            new Preference('es-ES', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('es-CO', 1, Preference::COMPLETE),
                            new Preference('es-*', 0.75, Preference::PARTIAL_WILDCARD)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('es-419', 1, Preference::COMPLETE)
                        )
                    )
                )
            )
        );
    }


    public function mimeProvider()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference('', 0, Preference::ABSENT_TYPE),
                new Preference('', 0, Preference::ABSENT_TYPE)
            )
        );

        return array(
            // There is nothing sensible we can do in this case
            'user_empty_app_empty' => array(
                'user' => '',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection($sort, array())
            ),

            // Pair must contain app type with highest quality factor
            'app_empty' => array(
                'user' => 'text/html,application/xml;q=0.75',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('text/html', 1.0, Preference::COMPLETE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('text/html', 1.0, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('application/xml', 0.75, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // Pair must contain user type with highest quality factor
            'user_empty' => array(
                'user' => 'application/rdf+xml;q=1,text/n3;q=0.5',
                'app' => '',
                'best' => new MatchedPreferences(
                    new Preference('application/rdf+xml', 1, Preference::COMPLETE),
                    new Preference('', 0, Preference::ABSENT_TYPE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('application/rdf+xml', 1, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('text/n3', 0.5, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        )
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'user_empty_app_identical_quality' => array(
                'user' => '',
                'app' => 'text/n3;q=0.5,text/html;q=0.5',
                'best' => new MatchedPreferences(
                    new Preference('', 0, Preference::ABSENT_TYPE),
                    new Preference('text/html', 0.5, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('text/html', 0.5, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('text/n3', 0.5, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the user-provided and app-omitted types have precedence over app-provided and user-omitted
            'multiple_matching_types' => array(
                'user' => 'application/xml;q=0.8,application/json;q=0.3,text/html;q=0.5',
                'app' => 'application/json;q=0.6,text/n3;q=0.9,text/html;q=0.3',
                'best' => new MatchedPreferences(
                    new Preference('application/json', 0.3, Preference::COMPLETE),
                    new Preference('application/json', 0.6, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('application/json', 0.3, Preference::COMPLETE),
                            new Preference('application/json', 0.6, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('text/html', 0.5, Preference::COMPLETE),
                            new Preference('text/html', 0.3, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('application/xml', 0.8, Preference::COMPLETE),
                            new Preference('', 0, Preference::ABSENT_TYPE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('text/n3', 0.9, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test subtype wildcard matching
            'subtype_wildcard' => array(
                'user' => 'text/*;q=0.8,application/xml;q=0.9',
                'app' => 'text/html,application/xml;q=0.7,text/n3;q=0.3',
                'best' => new MatchedPreferences(
                    new Preference('text/*', 0.8, Preference::PARTIAL_WILDCARD),
                    new Preference('text/html', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('text/*', 0.8, Preference::PARTIAL_WILDCARD),
                            new Preference('text/html', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('application/xml', 0.9, Preference::COMPLETE),
                            new Preference('application/xml', 0.7, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('text/*', 0.8, Preference::PARTIAL_WILDCARD),
                            new Preference('text/n3', 0.3, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test subtype wildcard precedence
            // If a wildcard and exact match have the came quality factor product the exact match is preferred
            'subtype_wildcard_precedence' => array(
                'user' => 'text/*;q=0.75,text/html',
                'app' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new MatchedPreferences(
                    new Preference('text/html', 1, Preference::COMPLETE),
                    new Preference('text/html', 0.75, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('text/html', 1, Preference::COMPLETE),
                            new Preference('text/html', 0.75, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('text/*', 0.75, Preference::PARTIAL_WILDCARD),
                            new Preference('text/plain', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('', 0, Preference::ABSENT_TYPE),
                            new Preference('application/xml', 0.9, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // Test full wildcard matching
            'full_wildcard' => array(
                'user' => '*/*;q=0.75,text/html',
                'app' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new MatchedPreferences(
                    new Preference('text/html', 1, Preference::COMPLETE),
                    new Preference('text/html', 0.75, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('text/html', 1, Preference::COMPLETE),
                            new Preference('text/html', 0.75, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*/*', 0.75, Preference::WILDCARD),
                            new Preference('text/plain', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*/*', 0.75, Preference::WILDCARD),
                            new Preference('application/xml', 0.9, Preference::COMPLETE)
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
                'best' => new MatchedPreferences(
                    new Preference('text/html', 1, Preference::COMPLETE),
                    new Preference('text/html', 1, Preference::COMPLETE)
                ),
                'all' => new MatchedPreferencesCollection(
                    $sort,
                    array(
                        new MatchedPreferences(
                            new Preference('text/html', 1, Preference::COMPLETE),
                            new Preference('text/html', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('text/*', 1, Preference::PARTIAL_WILDCARD),
                            new Preference('text/plain', 1, Preference::COMPLETE)
                        ),
                        new MatchedPreferences(
                            new Preference('*/*', 1, Preference::WILDCARD),
                            new Preference('application/xml', 1, Preference::COMPLETE)
                        )
                    )
                )
            ),

            // TODO: Test with presence of accept-extens components
        );
    }
}
