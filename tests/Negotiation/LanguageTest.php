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

use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

class LanguageTest extends NegotiationDataProvider
{
    /**
     * @dataProvider languageProvider
     *
     * @param string $clientField
     * @param string $serverField
     * @param PreferenceInterface $best
     * @param MatchedPreferencesInterface[] $all
     */
    public function testBest($clientField, $serverField, PreferenceInterface $best, array $all)
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
     * @param PreferenceInterface $best
     * @param MatchedPreferencesInterface[] $all
     */
    public function testAll($clientField, $serverField, PreferenceInterface $best, array $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->languageAll($clientField, $serverField);

        $this->assertEquals($all, $collection);
    }
}
