<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

class CharsetTest extends NegotiationDataProvider
{
    /**
     * @dataProvider charsetProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testBest($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $resultType = $negotiate->charsetBest($clientField, $serverField);

        $this->assertEquals($best, $resultType);
    }

    /**
     * @dataProvider charsetProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testAll($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->charsetAll($clientField, $serverField);

        $this->assertEquals($all, $collection);
    }
}
