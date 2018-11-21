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
 * Regression tests for Issue #2.
 */
class IssueTwoTest extends TestCase
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
                Preference::MIME,
                new Preference('text/turtle', 1, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('application/rdf+json', 0.8, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('application/rdf+xml', 0.8, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('text/n3', 0.8, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('text/rdf+n3', 0.8, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            ),
            new MatchedPreference(
                Preference::MIME,
                new Preference('application/ld+json', 0.5, Preference::COMPLETE),
                new Preference('', 0, Preference::ABSENT)
            )
        );

        $negotiate = new Negotiation();
        $resultList = $negotiate->mimeAll($httpField, $serverPrefs);

        $this->assertEquals($expectList, $resultList);
    }
}
