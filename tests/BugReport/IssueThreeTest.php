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
 * Regression tests for Issue #3
 */
class IssueThreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testOne()
    {
        $httpField = 'application/rdf+xml;q=0.5,text/html;q=.3';
        $serverPrefs = '';

        $expectList = array(
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/rdf+xml', 0.5, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }


    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testTwo()
    {
        $httpField = 'application/xhtml+xml;q=0.5';
        $serverPrefs = '';

        $expectList = array(
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/xhtml+xml', 0.5, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }


    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testThree()
    {
        $httpField = 'application/rdf+xml;q=0.5,text/html;q=.5';
        $serverPrefs = '';

        $expectList = array(
            new MatchedPreference(
                new Preference(Preference::MIME, 'application/rdf+xml', 0.5, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE)
            ),
            new MatchedPreference(
                new Preference(Preference::MIME, 'text/html', 0.5, Preference::COMPLETE),
                new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }
}
