<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Matcher;

use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher for charset, encoding & language types.
 */
class Matcher implements MatcherInterface
{
    /**
     * Empty type instance, used when only user & app types are asymmetric.
     *
     * @var PreferenceInterface
     */
    private $emptyType;

    /**
     * Instance of sorter than can reorder pairs.
     *
     * @var MatchedPreferencesSort
     */
    private $pairSort;


    /**
     * Constructor
     *
     * @param PreferenceInterface $emptyType
     * @param MatchedPreferencesSort $pairSort
     */
    public function __construct(PreferenceInterface $emptyType, MatchedPreferencesSort $pairSort)
    {
        $this->emptyType  = $emptyType;
        $this->pairSort     = $pairSort;
    }

    /**
     * Return a collection of types sorted by preference.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     *
     * @return MatchedPreferencesCollection
     */
    public function negotiateAll(array $userTypeList, array $appTypeList)
    {
        $matchingList = array();

        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new MatchedPreferences(
                $this->emptyType,
                $appType
            );
        }

        $matchingList = $this->matchUserToAppTypes($userTypeList, $matchingList);

        $pairList = array();
        foreach ($matchingList as $matching) {
            $pairList[] = $matching;
        }
        $pairCollection = new MatchedPreferencesCollection($this->pairSort, $pairList);

        return $pairCollection->getDescending();
    }

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     *
     * @return MatchedPreferencesInterface
     */
    public function negotiateBest(array $userTypeList, array $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }

    /**
     * Match user types to app types.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param MatchedPreferences[] $matchingList
     *
     * @return MatchedPreferences[]
     */
    private function matchUserToAppTypes(array $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {

            // Type match
            if (array_key_exists($userType->getType(), $matchingList)) {
                $matchingList = $this->matchExact($matchingList, $userType);

            // Wildcard Match
            } elseif (Preference::WILDCARD === $userType->getPrecedence()) {
                $matchingList = $this->matchFullWildcard($matchingList, $userType);

            // App Partial Lang Match
            // TODO: Only for Accept-Language field
            } elseif ($this->listHasPartialLanguage($matchingList, $userType)) {
                $matchingList = $this->matchAppPartialLanguage($matchingList, $userType);

            // No match
            } else {
                $matchingList[$userType->getType()] = new MatchedPreferences(
                    $userType,
                    $this->emptyType
                );
            }
        }

        return $matchingList;
    }

    /**
     * Returns true if the user type matches an application-provided partial language.
     *
     * @param MatchedPreferences[] $matchingList
     * @param PreferenceInterface $userType
     *
     * @return boolean
     */
    private function listHasPartialLanguage(array $matchingList, PreferenceInterface $userType)
    {
        $matches = false;
        foreach ($matchingList as $matching) {
            if (
                $this->partialLangMatches($matching->getAppType(), $userType)
                && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()
            ) {
                $matches = true;
            }
        }

        return $matches;
    }

    /**
     * Returns true if the app type contains a partial language that matches the language in the user type.
     *
     * e.g. An application type of en-* would match en, en-US but not es-ES
     *
     * @param PreferenceInterface $appType
     * @param PreferenceInterface $userType
     *
     * @return bool
     */
    private function partialLangMatches(PreferenceInterface $appType, PreferenceInterface $userType)
    {
        // Note that this only supports the simplest case of (e.g.) en-* matching en-GB and en-US, additional
        // Language tags are explicitly ignored
        list($userMainLang) = explode('-', $userType->getType());
        list($appMainLang) = explode('-', $appType->getType());

        return Preference::PARTIAL_WILDCARD === $appType->getPrecedence()
            && $userMainLang == $appMainLang;
    }

    /**
     * @param MatchedPreferences[]    $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferences[]
     */
    private function matchAppPartialLanguage(array $matchingList, PreferenceInterface $userType)
    {
        $newMatchingList = array();

        foreach ($matchingList as $key => $matching) {
            if (
                $this->partialLangMatches($matching->getAppType(), $userType)
                && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()
            ) {
                $newPair = new MatchedPreferences(
                    $userType,
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
     * Attempt to find an exact match with type in matching list.
     *
     * @param MatchedPreferences[]    $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferences[]
     */
    private function matchExact(array $matchingList, PreferenceInterface $userType)
    {
        $newPair = new MatchedPreferences(
            $userType,
            $matchingList[$userType->getType()]->getAppType()
        );

        if ($this->pairSort->compare($matchingList[$userType->getType()], $newPair) > 0) {
            $matchingList[$userType->getType()] = $newPair;
        }

        return $matchingList;
    }

    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param MatchedPreferences[]    $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferences[]
     */
    private function matchFullWildcard(array $matchingList, PreferenceInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new MatchedPreferences(
                    $userType,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }
}
