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
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesComparator;
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
     * @var PreferenceBuilderInterface
     */
    private $mimePreferenceBuilder;

    /**
     * @var MatcherInterface[]
     */
    private $matcherList;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $preferenceBuilder
     * @param PreferenceBuilderInterface $mimePreferenceBuilder
     * @param MatcherInterface[] $matcherList Objects implementing MatcherInterface, matching is attempted in the order
     *      of the matcher objects in the array. When a match is found no further match tests are done and the testing
     *      loop will exit.
     */
    public function __construct(
        PreferenceBuilderInterface $preferenceBuilder,
        PreferenceBuilderInterface $mimePreferenceBuilder,
        array $matcherList = array()
    ) {
        $this->preferenceBuilder = $preferenceBuilder;
        $this->mimePreferenceBuilder = $mimePreferenceBuilder;

        if (!count($matcherList)) {
            $matcherList = array(
                new WildcardMatcher(),
                new PartialLanguageMatcher(),
                new SubtypeWildcardMatcher(),
                new ExactMatcher(new MatchedPreferencesComparator()),
                new AbsentMatcher($this->preferenceBuilder, $this->mimePreferenceBuilder)
            );
        }
        $this->matcherList = $matcherList;
    }

    /**
     * @inheritDoc
     */
    public function negotiateAll(array $userPreferenceList, array $appPreferenceList, $fromField)
    {
        $emptyPreference = $this->getBuilder($fromField)
            ->setFromField($fromField)
            ->get();

        $sort = new MatchedPreferencesSort(new MatchedPreferences($emptyPreference, $emptyPreference));

        $matchingList = array();

        foreach ($appPreferenceList as $appPreference) {
            $matchingList[$appPreference->getType()] = new MatchedPreferences(
                $emptyPreference,
                $appPreference
            );
        }

        $matchingList = $this->matchUserListToAppPreferences($userPreferenceList, $matchingList);
        $pairCollection = new MatchedPreferencesCollection($sort, $matchingList);

        return $pairCollection->getDescending();
    }

    /**
     * @inheritDoc
     */
    public function negotiateBest(array $userPreferenceList, array $appPreferenceList, $fromField)
    {
        $pairCollection = $this->negotiateAll($userPreferenceList, $appPreferenceList, $fromField);

        return $pairCollection->getBest();
    }

    /**
     * Match user types to app types.
     *
     * @param PreferenceInterface[] $userPreferenceList
     * @param MatchedPreferencesInterface[] $matchingList
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchUserListToAppPreferences(array $userPreferenceList, array $matchingList) {
        foreach ($userPreferenceList as $userPreference) {
            $matchingList = $this->matchUserToAppPreferences($userPreference, $matchingList);
        }

        return $matchingList;
    }

    /**
     * Match a single user type to the application types.
     *
     * @param PreferenceInterface $userPreference
     * @param MatchedPreferencesInterface[] $matchingList
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchUserToAppPreferences(PreferenceInterface $userPreference, array $matchingList)
    {
        foreach ($this->matcherList as $matcher) {
            if ($matcher->hasMatch($matchingList, $userPreference)) {
                $matchingList = $matcher->doMatch($matchingList, $userPreference);

                break;
            }
        }

        return $matchingList;
    }

    /**
     * Get a preference builder for the specified HTTP field.
     *
     * @param string $fromField
     *
     * @return PreferenceBuilderInterface
     */
    private function getBuilder($fromField)
    {
        if (PreferenceInterface::MIME === $fromField) {
            return $this->mimePreferenceBuilder;
        } else {
            return $this->preferenceBuilder;
        }
    }
}
