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

class EncodingTest extends NegotiationDataProvider
{
    /**
     * @dataProvider encodingProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testBest($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $resultType = $negotiate->encodingBest($clientField, $serverField);

        $this->assertEquals($best, $resultType);
    }

    /**
     * @dataProvider encodingProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $best
     * @param MatchedPreferenceInterface[] $all
     */
    public function testAll($clientField, $serverField, $best, array $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->encodingAll($clientField, $serverField);

        $this->assertEquals($all, $collection);
    }
}
