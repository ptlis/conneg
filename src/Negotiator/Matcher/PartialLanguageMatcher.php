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
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher for server-provided partial languages (e.g. 'en-*').
 */
class PartialLanguageMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        $hasMatch = false;

        foreach ($matchingList as $matching) {
            if ($this->partialLangMatches($matching, $clientPref)) {
                $hasMatch = true;
            }
        }

        return $hasMatch;
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        $newMatchingList = array();

        foreach ($matchingList as $key => $matching) {
            if ($this->partialLangMatches($matching, $clientPref)) {
                $newPair = new MatchedPreferences(
                    $clientPref,
                    $matching->getServerPreference()
                );

                $newMatchingList[$key] = $newPair;

            } else {
                $newMatchingList[$key] = $matching;
            }
        }

        return $newMatchingList;
    }

    /**
     * Returns true if the server preference contains a partial language that matches the language in the client
     * preference.
     *
     * e.g. An server type of en-* would match en, en-US but not es-ES
     *
     * @param MatchedPreferences $matchedPreferences
     * @param PreferenceInterface $newClientPref
     *
     * @return bool
     */
    private function partialLangMatches(MatchedPreferences $matchedPreferences, PreferenceInterface $newClientPref) {
        $serverPref = $matchedPreferences->getServerPreference();
        $oldClientPref = $matchedPreferences->getClientPreference();

        // Note that this only supports the simplest case of (e.g.) en-* matching en-GB and en-US, additional
        // Language tags are explicitly ignored
        list($clientMainLang) = explode('-', $newClientPref->getType());
        list($serverMainLang) = explode('-', $serverPref->getType());

        return PreferenceInterface::LANGUAGE === $newClientPref->getFromField()
            && PreferenceInterface::PARTIAL_WILDCARD === $serverPref->getPrecedence()
            && $clientMainLang == $serverMainLang
            && $newClientPref->getPrecedence() > $oldClientPref->getPrecedence();
    }
}
