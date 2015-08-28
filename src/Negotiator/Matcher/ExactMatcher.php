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

use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceComparator;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher looking for exact type matches.
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
        return $this->getMatchingTypes($matchingList, $clientPref) >= 0;
    }

    /**
     * @inheritDoc
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        $matchIndex = $this->getMatchingTypes($matchingList, $clientPref);

        if ($matchIndex >= 0) {
            $newMatch = new MatchedPreference(
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
     * @param MatchedPreferenceInterface[] $matchingList
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
