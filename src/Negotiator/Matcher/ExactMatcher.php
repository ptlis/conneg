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

use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesComparator;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher looking for exact type matches.
 */
class ExactMatcher implements MatcherInterface
{
    /**
     * @var MatchedPreferencesComparator
     */
    private $comparator;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesComparator $comparator
     */
    public function __construct(MatchedPreferencesComparator $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        return $this->getMatchingTypes($matchingList, $clientPref) >= 0;
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        $matchIndex = $this->getMatchingTypes($matchingList, $clientPref);

        if ($matchIndex >= 0) {
            $newMatch = new MatchedPreferences(
                $clientPref,
                $matchingList[$matchIndex]->getServerPreference()
            );

            if ($this->comparator->compare($matchingList[$matchIndex], $newMatch) > 0) {
                $matchingList[$matchIndex] = $newMatch;
            }
        }

        return $matchingList;
    }

    /**
     * Returns the first index containing a matching type.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $pref
     *
     * @return int
     */
    private function getMatchingTypes(array $matchingList, PreferenceInterface $pref)
    {
        $index = -1;

        foreach ($matchingList as $key => $match) {
            if ($match->getType() === $pref->getType()) {
                $index = $key;
            }
        }

        return $index;
    }
}
