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
 * Matcher for application-provided partial languages (e.g. 'en-*').
 */
class PartialLanguageMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        $hasMatch = false;

        foreach ($matchingList as $matching) {
            if ($this->partialLangMatches($matching, $userPreference)) {
                $hasMatch = true;
            }
        }

        return $hasMatch;
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        $newMatchingList = array();

        foreach ($matchingList as $key => $matching) {
            if ($this->partialLangMatches($matching, $userPreference)) {
                $newPair = new MatchedPreferences(
                    $userPreference,
                    $matching->getAppType()
                );

                $newMatchingList[$key] = $newPair;

            } else {
                $newMatchingList[$key] = $matching;
            }
        }

        return $newMatchingList;
    }

    /**
     * Returns true if the app preference contains a partial language that matches the language in the user preference.
     *
     * e.g. An application type of en-* would match en, en-US but not es-ES
     *
     * @param MatchedPreferences $matchedPreferences
     * @param PreferenceInterface $newUserPreference
     *
     * @return bool
     */
    private function partialLangMatches(
        MatchedPreferences $matchedPreferences,
        PreferenceInterface $newUserPreference
    ) {
        $appPreference = $matchedPreferences->getAppType();
        $oldUserPreference = $matchedPreferences->getUserType();

        // Note that this only supports the simplest case of (e.g.) en-* matching en-GB and en-US, additional
        // Language tags are explicitly ignored
        list($userMainLang) = explode('-', $newUserPreference->getType());
        list($appMainLang) = explode('-', $appPreference->getType());

        return PreferenceInterface::LANGUAGE === $newUserPreference->getFromField()
            && PreferenceInterface::PARTIAL_WILDCARD === $appPreference->getPrecedence()
            && $userMainLang == $appMainLang
            && $newUserPreference->getPrecedence() > $oldUserPreference->getPrecedence();
    }
}
