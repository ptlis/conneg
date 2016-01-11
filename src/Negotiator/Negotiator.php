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
use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceComparator;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceSort;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Negotiator for Accept-Charset, Accept-Encoding & Accept-Language fields.
 */
class Negotiator implements NegotiatorInterface
{
    /**
     * @var PreferenceBuilderInterface
     */
    private $prefBuilder;

    /**
     * @var PreferenceBuilderInterface
     */
    private $mimePrefBuilder;

    /**
     * @var MatcherInterface[]
     */
    private $matcherList;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $prefBuilder
     * @param PreferenceBuilderInterface $mimePrefBuilder
     * @param MatcherInterface[] $matcherList Objects implementing MatcherInterface, matching is attempted in the order
     *      of the matcher objects in the array. When a match is found no further match tests are done and the testing
     *      loop will exit.
     */
    public function __construct(
        PreferenceBuilderInterface $prefBuilder,
        PreferenceBuilderInterface $mimePrefBuilder,
        array $matcherList = array()
    ) {
        $this->prefBuilder = $prefBuilder;
        $this->mimePrefBuilder = $mimePrefBuilder;

        if (!count($matcherList)) {
            $matcherList = array(
                new WildcardMatcher(),
                new PartialLanguageMatcher(),
                new SubtypeWildcardMatcher(),
                new ExactMatcher(new MatchedPreferenceComparator()),
                new AbsentMatcher($this->prefBuilder, $this->mimePrefBuilder)
            );
        }
        $this->matcherList = $matcherList;
    }

    /**
     * @inheritDoc
     */
    public function negotiateAll(array $clientPrefList, array $serverPrefList, $fromField)
    {
        $emptyPref = $this->getBuilder($fromField)
            ->setFromField($fromField)
            ->get();

        $sort = new MatchedPreferenceSort();

        $matchingList = array();

        foreach ($serverPrefList as $serverPref) {
            $matchingList[] = new MatchedPreference(
                $fromField,
                $emptyPref,
                $serverPref
            );
        }

        $matchingList = $this->matchClientPreferences($fromField, $clientPrefList, $matchingList);

        return $sort->sortDescending($matchingList);
    }

    /**
     * @inheritDoc
     */
    public function negotiateBest(array $clientPrefList, array $serverPrefList, $fromField)
    {
        $pairCollection = $this->negotiateAll($clientPrefList, $serverPrefList, $fromField);

        if (count($pairCollection)) {
            $best = $pairCollection[0];
        } else {
            $emptyPref = $this->getBuilder($fromField)
                ->setFromField($fromField)
                ->get();

            $best = new MatchedPreference($fromField, $emptyPref, $emptyPref);
        }

        return $best;
    }

    /**
     * Match client variants to server variants.
     *
     * @param string $fromField
     * @param PreferenceInterface[] $clientPrefList
     * @param MatchedPreferenceInterface[] $matchingList
     *
     * @return MatchedPreferenceInterface[]
     */
    private function matchClientPreferences($fromField, array $clientPrefList, array $matchingList)
    {
        foreach ($clientPrefList as $clientPref) {
            $matchingList = $this->matchSingleClientPreference($fromField, $clientPref, $matchingList);
        }

        return $matchingList;
    }

    /**
     * Match a single client variant to the server variants.
     *
     * @param string $fromField
     * @param PreferenceInterface $clientPreference
     * @param MatchedPreferenceInterface[] $matchingList
     *
     * @return MatchedPreferenceInterface[]
     */
    private function matchSingleClientPreference($fromField, PreferenceInterface $clientPreference, array $matchingList)
    {
        foreach ($this->matcherList as $matcher) {
            if ($matcher->hasMatch($fromField, $matchingList, $clientPreference)) {
                $matchingList = $matcher->match($fromField, $matchingList, $clientPreference);

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
            return $this->mimePrefBuilder;
        } else {
            return $this->prefBuilder;
        }
    }
}
