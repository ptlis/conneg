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
 * Matcher for server-provided partial languages (e.g. 'en-*').
 */
final class PartialLanguageMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch(string $fromField, array $matchingList, PreferenceInterface $clientPref): bool
    {
        return count($this->getMatchingIndexes($fromField, $matchingList, $clientPref)) > 0;
    }

    /**
     * @inheritDoc
     */
    public function match(string $fromField, array $matchingList, PreferenceInterface $clientPref): array
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
    private function getMatchingIndexes(string $fromField, array $matchingList, PreferenceInterface $clientPref): array
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
        string $fromField,
        MatchedPreferenceInterface $matchedPreference,
        PreferenceInterface $newClientPref
    ): bool {
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
