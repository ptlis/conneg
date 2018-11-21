<?php declare(strict_types=1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

class LanguageTest extends NegotiationDataProvider
{
    /**
     * @dataProvider languageProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testBest($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $resultType = $negotiate->languageBest($clientField, $serverField);

        $this->assertEquals($best, $resultType);
    }

    /**
     * @dataProvider languageProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testAll($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->languageAll($clientField, $serverField);

        $this->assertEquals($all, $collection);
    }
}
