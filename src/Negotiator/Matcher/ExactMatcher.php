<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Negotiator\Matcher;

use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceComparator;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher looking for exact variant matches.
 */
class ExactMatcher implements MatcherInterface
{
    /**
     * @var MatchedPreferenceComparator
     */
    private $comparator;


    /**
     * Constructor.
     *
     * @param MatchedPreferenceComparator $comparator
     */
    public function __construct(MatchedPreferenceComparator $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @inheritDoc
     */
    public function hasMatch($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        return $this->getMatchingIndex($matchingList, $clientPref) >= 0;
    }

    /**
     * @inheritDoc
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        $matchIndex = $this->getMatchingIndex($matchingList, $clientPref);

        if ($matchIndex >= 0) {
            $newMatch = new MatchedPreference(
                $fromField,
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
     * Returns the first index containing a matching variant, or -1 if not present.
     *
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $pref
     *
     * @return int
     */
    private function getMatchingIndex(array $matchingList, PreferenceInterface $pref)
    {
        $index = -1;

        foreach ($matchingList as $key => $match) {
            if ($match->getVariant() === $pref->getVariant()) {
                $index = $key;
            }
        }

        return $index;
    }
}
