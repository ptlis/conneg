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

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Negotiator\Matcher\AbsentMatcher;
use ptlis\ConNeg\Negotiator\Matcher\ExactMatcher;
use ptlis\ConNeg\Negotiator\Matcher\MatcherInterface;
use ptlis\ConNeg\Negotiator\Matcher\PartialLanguageMatcher;
use ptlis\ConNeg\Negotiator\Matcher\SubtypeWildcardMatcher;
use ptlis\ConNeg\Negotiator\Matcher\WildcardMatcher;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Negotiator for charset, encoding & language types.
 */
class Negotiator implements NegotiatorInterface
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

        $matcherList = array(
            new WildcardMatcher(),
            new PartialLanguageMatcher(),
            new SubtypeWildcardMatcher(),
            new ExactMatcher($sort),
            new AbsentMatcher($emptyType)
        );

        /** @var MatcherInterface $matcher */
        foreach ($matcherList as $matcher) {
            if ($matcher->hasMatch($matchingList, $userType)) {
                $matchingList = $matcher->doMatch($matchingList, $userType);

                break;
            }
        }

        return $matchingList;
    }
}
