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

use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher for charset, encoding & language types.
 */
class Matcher extends AbstractMatcher
{
    /**
     * @var PreferenceBuilderInterface
     */
    private $preferenceBuilder;


    /**
     * Constructor
     *
     * @param PreferenceBuilderInterface $preferenceBuilder
     */
    public function __construct(PreferenceBuilderInterface $preferenceBuilder)
    {
        $this->preferenceBuilder = $preferenceBuilder;
    }

    /**
     * @inheritDoc
     */
    public function negotiateAll(array $userTypeList, array $appTypeList, $fromField)
    {
        $emptyType = $this->preferenceBuilder
            ->setFromField($fromField)
            ->get();

        $sort = new MatchedPreferencesSort(new MatchedPreferences($emptyType, $emptyType));

        $matchingList = array();

        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new MatchedPreferences(
                $emptyType,
                $appType
            );
        }

        $matchingList = $this->matchUserListToAppTypes($userTypeList, $matchingList, $sort, $emptyType);
        $pairCollection = new MatchedPreferencesCollection($sort, $matchingList);

        return $pairCollection->getDescending();
    }

    /**
     * @inheritDoc
     */
    public function negotiateBest(array $userTypeList, array $appTypeList, $fromField)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList, $fromField);

        return $pairCollection->getBest();
    }

    /**
     * Match user types to app types.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param MatchedPreferencesInterface[] $matchingList
     * @param MatchedPreferencesSort $sort
     * @param PreferenceInterface $emptyType
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchUserListToAppTypes(
        array $userTypeList,
        array $matchingList,
        MatchedPreferencesSort $sort,
        $emptyType
    ) {
        foreach ($userTypeList as $userType) {
            $matchingList = $this->matchUserToAppTypes($userType, $matchingList, $sort, $emptyType);
        }

        return $matchingList;
    }

    /**
     * Match a single user type to the application types.
     *
     * @param PreferenceInterface $userType
     * @param MatchedPreferencesInterface[] $matchingList
     * @param MatchedPreferencesSort $sort
     * @param PreferenceInterface $emptyType
     *
     * @return  array<string,MatchedPreferencesInterface>
     */
    private function matchUserToAppTypes(
        PreferenceInterface $userType,
        array $matchingList,
        MatchedPreferencesSort $sort,
        $emptyType
    ) {
        switch (true) {
            // Full wildcard match
            case Preference::WILDCARD === $userType->getPrecedence():
                $matchingList = $this->matchFullWildcard($matchingList, $userType);
                break;

            // Partial wildcard match
            case $this->listHasPartialMatch($matchingList, $userType):
                $matchingList = $this->matchPartialWildcard($matchingList, $userType);
                break;

            // Exact match
            case array_key_exists($userType->getType(), $matchingList):
                $matchingList = $this->matchExact($matchingList, $userType, $sort);
                break;

            // No match
            default:
                $matchingList[$userType->getType()] = new MatchedPreferences(
                    $userType,
                    $emptyType
                );
                break;
        }

        return $matchingList;
    }

    /**
     * Attempt to find an exact match with type in matching list.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userType
     * @param MatchedPreferencesSort $sort
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchExact(array $matchingList, PreferenceInterface $userType, MatchedPreferencesSort $sort)
    {
        $newPair = new MatchedPreferences(
            $userType,
            $matchingList[$userType->getType()]->getAppType()
        );

        if ($sort->compare($matchingList[$userType->getType()], $newPair) > 0) {
            $matchingList[$userType->getType()] = $newPair;
        }

        return $matchingList;
    }

    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferencesInterface[]
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

    /**
     * Returns true if the user type matches an application-provided partial language.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userType
     *
     * @return boolean
     */
    private function listHasPartialMatch(array $matchingList, PreferenceInterface $userType)
    {
        $matches = false;
        if (Preference::LANGUAGE === $userType->getFromField()) {
            foreach ($matchingList as $matching) {
                if (
                    $this->partialLangMatches($matching->getAppType(), $userType)
                    && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()
                ) {
                    $matches = true;
                }
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
     * Match the provided user type to an application-provided Language partial wildcard (e.g. en-*).
     *
     * @param MatchedPreferences[] $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferences[]
     */
    private function matchPartialWildcard(array $matchingList, PreferenceInterface $userType)
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
}
