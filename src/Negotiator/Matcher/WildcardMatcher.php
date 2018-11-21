<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Negotiator\Matcher;

use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher for full wildcards (e.g. '*' and '*\/*
 */
class WildcardMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        return PreferenceInterface::WILDCARD === $clientPref->getPrecedence()
            && count($this->getMatchingIndexes($matchingList, $clientPref));
    }

    /**
     * @inheritDoc
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        $matchingIndexList = $this->getMatchingIndexes($matchingList, $clientPref);

        foreach ($matchingIndexList as $matchingIndex) {
            $matchingList[$matchingIndex] = new MatchedPreference(
                $fromField,
                $clientPref,
                $matchingList[$matchingIndex]->getServerPreference()
            );
        }

        return $matchingList;
    }

    /**
     * Returns an array of indexes for of matches of higher precedence than the existing pairing.
     *
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $clientPref
     *
     * @return int[]
     */
    private function getMatchingIndexes(array $matchingList, PreferenceInterface $clientPref)
    {
        $matchingIndexList = array();

        foreach ($matchingList as $key => $matching) {
            if ($clientPref->getPrecedence() > $matching->getClientPreference()->getPrecedence()) {
                $matchingIndexList[] = $key;
            }
        }

        return $matchingIndexList;
    }
}
