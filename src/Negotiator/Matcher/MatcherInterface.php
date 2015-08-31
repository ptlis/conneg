<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Negotiator\Matcher;

use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface used by classes implementing matching strategies.
 */
interface MatcherInterface
{
    /**
     * Returns true if this matcher can do matching for the client preference.
     *
     * @parma string $fromField
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $clientPref
     *
     * @return bool
     */
    public function hasMatch($fromField, array $matchingList, PreferenceInterface $clientPref);

    /**
     * Perform the matching of client preference to server-provided preference, returning a new array containing the
     * result of the matching operation.
     *
     * @parma string $fromField
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $clientPref
     *
     * @return MatchedPreferenceInterface[]
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref);
}