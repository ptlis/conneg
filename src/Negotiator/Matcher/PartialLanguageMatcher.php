<?php

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
 * Matcher for server-provided partial languages (e.g. 'en-*').
 */
class PartialLanguageMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        return count($this->getMatchingIndexes($fromField, $matchingList, $clientPref)) > 0;
    }

    /**
     * @inheritDoc
     */
    public function match($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        $matchingIndexList = $this->getMatchingIndexes($fromField, $matchingList, $clientPref);

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
     * @param string $fromField
     * @param MatchedPreferenceInterface[] $matchingList
     * @param PreferenceInterface $clientPref
     *
     * @return int[]
     */
    private function getMatchingIndexes($fromField, array $matchingList, PreferenceInterface $clientPref)
    {
        $matchingIndexList = array();

        foreach ($matchingList as $key => $matching) {
            if ($this->partialLangMatches($fromField, $matching, $clientPref)) {
                $matchingIndexList[] = $key;
            }
        }

        return $matchingIndexList;
    }

    /**
     * Returns true if the server preference contains a partial language that matches the language in the client
     * preference.
     *
     * e.g. An server variant of en-* would match en, en-US but not es-ES
     *
     * @param string $fromField
     * @param MatchedPreferenceInterface $matchedPreference
     * @param PreferenceInterface $newClientPref
     *
     * @return bool
     */
    private function partialLangMatches(
        $fromField,
        MatchedPreferenceInterface $matchedPreference,
        PreferenceInterface $newClientPref
    ) {
        $serverPref = $matchedPreference->getServerPreference();
        $oldClientPref = $matchedPreference->getClientPreference();

        // Note that this only supports the simplest case of (e.g.) en-* matching en-GB and en-US, additional
        // Language tags are explicitly ignored
        list($clientMainLang) = explode('-', $newClientPref->getVariant());
        list($serverMainLang) = explode('-', $serverPref->getVariant());

        return PreferenceInterface::LANGUAGE === $fromField
            && PreferenceInterface::PARTIAL_WILDCARD === $serverPref->getPrecedence()
            && $clientMainLang == $serverMainLang
            && $newClientPref->getPrecedence() > $oldClientPref->getPrecedence();
    }
}
