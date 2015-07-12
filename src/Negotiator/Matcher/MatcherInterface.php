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

use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface used by classes implementing matching strategies.
 */
interface MatcherInterface
{
    /**
     * Returns true if this matcher can do matching for the user preference.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userPreference
     *
     * @return bool
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference);

    /**
     * Perform the matching of user preference to application provided preference.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userPreference
     *
     * @return MatchedPreferencesInterface[]
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference);
}
