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
 * Matcher for mime types.
 */
class MimeMatcher implements MatcherInterface
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
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param MatchedPreferencesInterface[]  $matchingList
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
     * Attempt to match wildcard subtypes against each item in matching list with an identical type.
     *
     * @param MatchedPreferencesInterface[]   $matchingList
     * @param PreferenceInterface     $userType
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchSubTypeWildcard(array $matchingList, PreferenceInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            $appType = $matching->getAppType();
            list($userMimeType) = explode('/', $userType->getType());
            list($appMimeType) = explode('/', $appType->getType());

            if ($userMimeType == $appMimeType
                    && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {

                $matchingList[$key] = new MatchedPreferences(
                    $userType,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }

    /**
     * Attempt to match the given type to an existing type in typeList.
     *
     * @param MatchedPreferencesInterface[] $matchingList
     * @param PreferenceInterface $userType
     * @param MatchedPreferencesSort $sort
     *
     * @return array<string,MatchedPreferencesInterface>
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
     * Match user types to app types.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param MatchedPreferencesInterface[] $matchingList
     * @param MatchedPreferencesSort $sort
     * @param PreferenceInterface $emptyType
     *
     * @return  array<string,MatchedPreferencesInterface>
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
            // Full Wildcard Match
            case Preference::WILDCARD === $userType->getPrecedence():
                $matchingList = $this->matchFullWildcard($matchingList, $userType);
                break;

            // Wildcard SubType Match
            case Preference::PARTIAL_WILDCARD === $userType->getPrecedence():
                $matchingList = $this->matchSubTypeWildcard($matchingList, $userType);
                break;

            // Exact Match
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
}
