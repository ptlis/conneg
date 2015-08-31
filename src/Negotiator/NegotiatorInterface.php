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
