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
        if (PreferenceInterface::LANGUAGE === $userPreference->getFromField()) {
            foreach ($matchingList as $matching) {
                if (
                    $this->partialLangMatches($matching->getAppType(), $userPreference)
                    && $userPreference->getPrecedence() > $matching->getUserType()->getPrecedence()
                ) {
                    $hasMatch = true;
                }
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
            if (
                $this->partialLangMatches($matching->getAppType(), $userPreference)
                && $userPreference->getPrecedence() > $matching->getUserType()->getPrecedence()
            ) {
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
     * @param PreferenceInterface $appPreference
     * @param PreferenceInterface $userPreference
     *
     * @return bool
     */
    private function partialLangMatches(PreferenceInterface $appPreference, PreferenceInterface $userPreference)
    {
        // Note that this only supports the simplest case of (e.g.) en-* matching en-GB and en-US, additional
        // Language tags are explicitly ignored
        list($userMainLang) = explode('-', $userPreference->getType());
        list($appMainLang) = explode('-', $appPreference->getType());

        return PreferenceInterface::PARTIAL_WILDCARD === $appPreference->getPrecedence()
            && $userMainLang == $appMainLang;
    }
}
