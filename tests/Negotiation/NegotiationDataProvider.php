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

use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;

abstract class NegotiationDataProvider extends \PHPUnit_Framework_TestCase
{
    public function charsetProvider()
    {
        return array(
            // There is nothing sensible we can do in this case
            'client_empty_server_empty' => array(
                'client' => '',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                    new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                ),
                'all' => array()
            ),

            // Pair must contain server type with highest quality factor
            'server_empty' => array(
                'client' => 'utf-8,iso-8859-5;q=0.75',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, 'utf-8', 1.0, Preference::COMPLETE),
                    new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'utf-8', 1.0, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'iso-8859-5', 0.75, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Pair must contain client type with highest quality factor
            'client_empty' => array(
                'client' => '',
                'server' => 'iso-8859-1;q=1,utf-8;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                    new Preference(Preference::CHARSET, 'iso-8859-1', 1.0, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                        new Preference(Preference::CHARSET, 'iso-8859-1', 1.0, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                        new Preference(Preference::CHARSET, 'utf-8', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'client_empty_server_identical_quality' => array(
                'client' => '',
                'server' => 'utf-8;q=0.5,iso-8859-1;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                    new Preference(Preference::CHARSET, 'iso-8859-1', 0.5, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                        new Preference(Preference::CHARSET, 'iso-8859-1', 0.5, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                        new Preference(Preference::CHARSET, 'utf-8', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the client-provided and server-omitted types have precedence over server-provided and client-omitted
            'multiple_matching_types' => array(
                'client' => 'windows-1250;q=0.8,utf-8;q=0.3,iso-8859-1;q=0.5',
                'server' => 'utf-8;q=0.6,iso-8859-5;q=0.9,iso-8859-1;q=0.3',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, 'utf-8', 0.3, Preference::COMPLETE),
                    new Preference(Preference::CHARSET, 'utf-8', 0.6, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'utf-8', 0.3, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, 'utf-8', 0.6, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'iso-8859-1', 0.5, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, 'iso-8859-1', 0.3, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'windows-1250', 0.8, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT),
                        new Preference(Preference::CHARSET, 'iso-8859-5', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'client' => 'iso-8859-5;q=0.3,utf-8;q=0.9,*;q=0.5',
                'server' => 'iso-8859-5,windows-1250',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, '*', 0.5, Preference::WILDCARD),
                    new Preference(Preference::CHARSET, 'windows-1250', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::CHARSET, 'windows-1250', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'iso-8859-5', 0.3, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'utf-8', 0.9, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'client' => '*;q=0.5,iso-8859-5;q=0.5',
                'server' => 'iso-8859-5,windows-1250',
                'best' => new MatchedPreference(
                    new Preference(Preference::CHARSET, 'iso-8859-5', 0.5, Preference::COMPLETE),
                    new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, 'iso-8859-5', 0.5, Preference::COMPLETE),
                        new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::CHARSET, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::CHARSET, 'windows-1250', 1, Preference::COMPLETE)
                    )
                )
            )
        );
    }

    public function encodingProvider()
    {
        return array(
            // There is nothing sensible we can do in this case
            'client_empty_server_empty' => array(
                'client' => '',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                    new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                ),
                'all' => array()
            ),

            // Pair must contain server type with highest quality factor
            'server_empty' => array(
                'client' => '7zip,gzip;q=0.75',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '7zip', 1.0, Preference::COMPLETE),
                    new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '7zip', 1.0, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, 'gzip', 0.75, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Pair must contain client type with highest quality factor
            'client_empty' => array(
                'client' => '',
                'server' => 'compress;q=1,7zip;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                    new Preference(Preference::ENCODING, 'compress', 1.0, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                        new Preference(Preference::ENCODING, 'compress', 1.0, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                        new Preference(Preference::ENCODING, '7zip', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'client_empty_server_identical_quality' => array(
                'client' => '',
                'server' => 'compress;q=0.5, 7zip;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                    new Preference(Preference::ENCODING, '7zip', 0.5, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                        new Preference(Preference::ENCODING, '7zip', 0.5, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                        new Preference(Preference::ENCODING, 'compress', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the client-provided and server-omitted types have precedence over server-provided and client-omitted
            'multiple_matching_types' => array(
                'client' => 'compress;q=0.8,7zip;q=0.3,deflate;q=0.5',
                'server' => '7zip;q=0.6,x-propriatary;q=0.9,deflate;q=0.3',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '7zip', 0.3, Preference::COMPLETE),
                    new Preference(Preference::ENCODING, '7zip', 0.6, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '7zip', 0.3, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '7zip', 0.6, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, 'deflate', 0.5, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, 'deflate', 0.3, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, 'compress', 0.8, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT),
                        new Preference(Preference::ENCODING, 'x-propriatary', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'client' => 'compress;q=0.3,7zip;q=0.9,*;q=0.5',
                'server' => 'compress,deflate',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '*', 0.5, Preference::WILDCARD),
                    new Preference(Preference::ENCODING, 'deflate', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::ENCODING, 'deflate', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, 'compress', 0.3, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, 'compress', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '7zip', 0.9, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'client' => '*;q=0.5,7zip;q=0.5',
                'server' => '7zip,compress',
                'best' => new MatchedPreference(
                    new Preference(Preference::ENCODING, '7zip', 0.5, Preference::COMPLETE),
                    new Preference(Preference::ENCODING, '7zip', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '7zip', 0.5, Preference::COMPLETE),
                        new Preference(Preference::ENCODING, '7zip', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::ENCODING, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::ENCODING, 'compress', 1, Preference::COMPLETE)
                    )
                )
            )
        );
    }

    public function languageProvider()
    {
        return array(
            // There is nothing sensible we can do in this case
            'client_empty_server_empty' => array(
                'client' => '',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                    new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                ),
                'all' => array()
            ),

            // Pair must contain server type with highest quality factor
            'server_empty' => array(
                'client' => 'en-GB,es;q=0.75',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, 'en-GB', 1.0, Preference::COMPLETE),
                    new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en-GB', 1.0, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'es', 0.75, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Pair must contain client type with highest quality factor
            'client_empty' => array(
                'client' => '',
                'server' => 'de;q=1,fr;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                    new Preference(Preference::LANGUAGE, 'de', 1.0, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'de', 1.0, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'fr', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'client_empty_server_identical_quality' => array(
                'client' => '',
                'server' => 'af;q=0.5, bg;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                    new Preference(Preference::LANGUAGE, 'af', 0.5, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'af', 0.5, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'bg', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the client-provided and server-omitted types have precedence over server-provided and client-omitted
            'multiple_matching_types' => array(
                'client' => 'en;q=0.8,en-GB;q=0.3,de;q=0.5',
                'server' => 'en-GB;q=0.6,cs;q=0.9,de;q=0.3',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, 'en-GB', 0.3, Preference::COMPLETE),
                    new Preference(Preference::LANGUAGE, 'en-GB', 0.6, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en-GB', 0.3, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'en-GB', 0.6, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'de', 0.5, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'de', 0.3, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en', 0.8, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'cs', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test wildcards - the wildcard match has higher quality factor product
            'test_wildcard_qualities' => array(
                'client' => 'en-GB;q=0.3,de;q=0.9,*;q=0.5',
                'server' => 'en-GB,fr',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, '*', 0.5, Preference::WILDCARD),
                    new Preference(Preference::LANGUAGE, 'fr', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::LANGUAGE, 'fr', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en-GB', 0.3, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'en-GB', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'de', 0.9, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Test wildcards - the non-wildcard match has higher precedence
            'test_wildcard_precedence' => array(
                'client' => '*;q=0.5,en-GB;q=0.5',
                'server' => 'en-GB,en-US',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, 'en-GB', 0.5, Preference::COMPLETE),
                    new Preference(Preference::LANGUAGE, 'en-GB', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en-GB', 0.5, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'en-GB', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '*', 0.5, Preference::WILDCARD),
                        new Preference(Preference::LANGUAGE, 'en-US', 1, Preference::COMPLETE)
                    )
                )
            ),

            // Test matching of partial language wildcards to languages
            'test_partial_language' => array(
                'client' => 'en-GB,es',
                'server' => 'es-*;q=0.75,es-ES,es-419',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, 'es', 1, Preference::COMPLETE),
                    new Preference(Preference::LANGUAGE, 'es-*', 0.75, Preference::PARTIAL_WILDCARD)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'es', 1, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'es-*', 0.75, Preference::PARTIAL_WILDCARD)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'en-GB', 1, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'es-419', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'es-ES', 1, Preference::COMPLETE)
                    )
                )
            ),

            // Test matching of partial language wildcards matching subtypes
            'test_partial_language_subtype' => array(
                'client' => 'es-CO,es-ES',
                'server' => 'es-*;q=0.75,es-ES,es-419',
                'best' => new MatchedPreference(
                    new Preference(Preference::LANGUAGE, 'es-ES', 1, Preference::COMPLETE),
                    new Preference(Preference::LANGUAGE, 'es-ES', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'es-ES', 1, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'es-ES', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, 'es-CO', 1, Preference::COMPLETE),
                        new Preference(Preference::LANGUAGE, 'es-*', 0.75, Preference::PARTIAL_WILDCARD)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT),
                        new Preference(Preference::LANGUAGE, 'es-419', 1, Preference::COMPLETE)
                    )
                )
            )
        );
    }


    public function mimeProvider()
    {
        return array(
            // There is nothing sensible we can do in this case
            'client_empty_server_empty' => array(
                'client' => '',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                    new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                ),
                'all' => array()
            ),

            // Pair must contain server type with highest quality factor
            'server_empty' => array(
                'client' => 'text/html,application/xml;q=0.75',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'text/html', 1.0, Preference::COMPLETE),
                    new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/html', 1.0, Preference::COMPLETE),
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'application/xml', 0.75, Preference::COMPLETE),
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // Pair must contain client type with highest quality factor
            'client_empty' => array(
                'client' => 'application/rdf+xml;q=1,text/n3;q=0.5',
                'server' => '',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'application/rdf+xml', 1, Preference::COMPLETE),
                    new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'application/rdf+xml', 1, Preference::COMPLETE),
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/n3', 0.5, Preference::COMPLETE),
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                    )
                )
            ),

            // When types have matching quality factors the result should be ordered alphabetically - note that this
            // isn't specification defined, but done to ensure that the sort is stable
            'client_empty_server_identical_quality' => array(
                'client' => '',
                'server' => 'text/n3;q=0.5,text/html;q=0.5',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                    new Preference(Preference::MIME, 'text/html', 0.5, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                        new Preference(Preference::MIME, 'text/html', 0.5, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                        new Preference(Preference::MIME, 'text/n3', 0.5, Preference::COMPLETE)
                    )
                )
            ),

            // Test when we have multiple matching types - when ordering type pairs where the type is omitted on one
            // side the client-provided and server-omitted types have precedence over server-provided and client-omitted
            'multiple_matching_types' => array(
                'client' => 'application/xml;q=0.8,application/json;q=0.3,text/html;q=0.5',
                'server' => 'application/json;q=0.6,text/n3;q=0.9,text/html;q=0.3',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'application/json', 0.3, Preference::COMPLETE),
                    new Preference(Preference::MIME, 'application/json', 0.6, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'application/json', 0.3, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'application/json', 0.6, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/html', 0.5, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'application/xml', 0.8, Preference::COMPLETE),
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                        new Preference(Preference::MIME, 'text/n3', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test subtype wildcard matching
            'subtype_wildcard' => array(
                'client' => 'text/*;q=0.8,application/xml;q=0.9',
                'server' => 'text/html,application/xml;q=0.7,text/n3;q=0.3',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'text/*', 0.8, Preference::PARTIAL_WILDCARD),
                    new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/*', 0.8, Preference::PARTIAL_WILDCARD),
                        new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'application/xml', 0.9, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'application/xml', 0.7, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/*', 0.8, Preference::PARTIAL_WILDCARD),
                        new Preference(Preference::MIME, 'text/n3', 0.3, Preference::COMPLETE)
                    )
                )
            ),

            // Test subtype wildcard precedence
            // If a wildcard and exact match have the came quality factor product the exact match is preferred
            'subtype_wildcard_precedence' => array(
                'client' => 'text/*;q=0.75,text/html',
                'server' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                    new Preference(Preference::MIME, 'text/html', 0.75, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'text/html', 0.75, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/*', 0.75, Preference::PARTIAL_WILDCARD),
                        new Preference(Preference::MIME, 'text/plain', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '', 0, Preference::ABSENT),
                        new Preference(Preference::MIME, 'application/xml', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test full wildcard matching
            'full_wildcard' => array(
                'client' => '*/*;q=0.75,text/html',
                'server' => 'text/plain,text/html;q=0.75,application/xml;q=0.9',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                    new Preference(Preference::MIME, 'text/html', 0.75, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'text/html', 0.75, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '*/*', 0.75, Preference::WILDCARD),
                        new Preference(Preference::MIME, 'text/plain', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '*/*', 0.75, Preference::WILDCARD),
                        new Preference(Preference::MIME, 'application/xml', 0.9, Preference::COMPLETE)
                    )
                )
            ),

            // Test full wildcard precedence
            // The full match has higher precedence, followed by partial wildcard match (e.g. text/*) and a full
            // wildcard match has the lowest precedence.
            'full_wildcard_precedence' => array(
                'client' => '*/*,text/*,text/html',
                'server' => 'text/plain,text/html,application/xml',
                'best' => new MatchedPreference(
                    new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                    new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE)
                ),
                'all' => array(
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE),
                        new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, 'text/*', 1, Preference::PARTIAL_WILDCARD),
                        new Preference(Preference::MIME, 'text/plain', 1, Preference::COMPLETE)
                    ),
                    new MatchedPreference(
                        new Preference(Preference::MIME, '*/*', 1, Preference::WILDCARD),
                        new Preference(Preference::MIME, 'application/xml', 1, Preference::COMPLETE)
                    )
                )
            ),

            // TODO: Test with presence of accept-extens components
        );
    }
}
