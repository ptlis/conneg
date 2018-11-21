<?php declare(strict_types=1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\BugReport;

use PHPUnit\Framework\TestCase;
use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;

/**
 * Regression tests for Issue #3
 */
class IssueThreeTest extends TestCase
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
                Preference::MIME,
                new Preference('application/rdf+xml', 0.5, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('text/html', 0.3, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
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
                Preference::MIME,
                new Preference('application/xhtml+xml', 0.5, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
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
                Preference::MIME,
                new Preference('application/rdf+xml', 0.5, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('text/html', 0.5, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }
}
