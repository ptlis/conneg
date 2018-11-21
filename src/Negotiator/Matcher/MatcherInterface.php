<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
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
     * @param string $fromField
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
     * @param string $fromField
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $clientPref
     *
     * @return MatchedPreferenceInterface[]
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref);
}
