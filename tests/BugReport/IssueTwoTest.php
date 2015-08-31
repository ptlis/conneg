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

namespace ptlis\ConNeg\Test\BugReport;

use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;

/**
 * Regression tests for Issue #2.
 */
class IssueTwoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link https://github.com/ptlis/conneg/issues/2 GitHub Issue #2.
     */
    public function testOne()
    {
        $httpField = 'text/rdf+n3; q=0.8, application/rdf+json; q=0.8, text/turtle; q=1.0, text/n3; q=0.8, application/ld+json; q=0.5, application/rdf+xml; q=0.8';
        $serverPrefs = '';

        $expectList = array(
            new MatchedPreference(
                new Preference(Preference::MIME, 'text/turtle', 1, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/rdf+json', 0.8, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/rdf+xml', 0.8, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'text/n3', 0.8, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'text/rdf+n3', 0.8, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/ld+json', 0.5, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }
}
