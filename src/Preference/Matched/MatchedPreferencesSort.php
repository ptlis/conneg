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

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Helper class encoding the rules governing the sorting of MatchedPreferenceCollections.
 */
class MatchedPreferencesSort
{
    /**
     * Default MatchedPreferences returned by 'best' negotiation when a collection is empty.
     *
     * @var MatchedPreferencesInterface
     */
    private $absentPreferences;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesInterface $absentPreferences  Default MatchedPreferences used for 'best' negotiation
     *      where the collection is empty.
     */
    public function __construct(MatchedPreferencesInterface $absentPreferences)
    {
        $this->absentPreferences = $absentPreferences;
    }

    /**
     * Sort the array of typePairs in ascending order.
     *
     * @param MatchedPreferencesInterface[] $preferencesList
     *
     * @return MatchedPreferencesCollection
     */
    public function sortAscending(array $preferencesList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $preferencesList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return -1 * $comparator->compare($lValue, $rValue);
            }
        );

        $newCollection = new MatchedPreferencesCollection($this, $preferencesList);

        return $newCollection;
    }

    /**
     * Sort the array of MatchedPreferences in descending order.
     *
     * @param MatchedPreferencesInterface[] $preferencesList
     *
     * @return MatchedPreferencesCollection
     */
    public function sortDescending(array $preferencesList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $preferencesList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return $comparator->compare($lValue, $rValue);
            }
        );

        $newCollection = new MatchedPreferencesCollection($this, $preferencesList);

        return $newCollection;
    }

    /**
     * Get the best matching MatchedPreferences.
     *
     * @param MatchedPreferencesInterface[] $preferencesList
     *
     * @return MatchedPreferencesInterface
     */
    public function getBest(array $preferencesList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $preferencesList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return $comparator->compare($lValue, $rValue);
            }
        );

        if (count($preferencesList)) {
            $bestPair = $preferencesList[0];

        } else {
            $bestPair = $this->absentPreferences;
        }

        return $bestPair;
    }
}
