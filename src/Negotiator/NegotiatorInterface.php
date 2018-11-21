<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface class that negotiators must implement.
 */
interface NegotiatorInterface
{
    /**
     * Return an array of variant preferences, sorted descending by preference .
     *
     * @param PreferenceInterface[] $clientPrefList
     * @param PreferenceInterface[] $serverPrefList
     * @param string $fromField
     *
     * @return MatchedPreferenceInterface[] Array containing preference intersection, descending order.
     */
    public function negotiateAll(array $clientPrefList, array $serverPrefList, $fromField);

    /**
     * Return the preferred variant.
     *
     * @param PreferenceInterface[] $clientPrefList
     * @param PreferenceInterface[] $serverPrefList
     * @param string $fromField
     *
     * @return MatchedPreferenceInterface The preferred variant.
     */
    public function negotiateBest(array $clientPrefList, array $serverPrefList, $fromField);
}
