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
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher looking for exact type matches.
 */
class ExactMatcher implements MatcherInterface
{
    /**
     * @var MatchedPreferencesSort
     */
    private $sort;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesSort $sort
     */
    public function __construct(MatchedPreferencesSort $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        return array_key_exists($userPreference->getType(), $matchingList);
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        $newMatch = new MatchedPreferences(
            $userPreference,
            $matchingList[$userPreference->getType()]->getAppPreference()
        );

        if ($this->sort->compare($matchingList[$userPreference->getType()], $newMatch) > 0) {
            $matchingList[$userPreference->getType()] = $newMatch;
        }

        return $matchingList;
    }
}
