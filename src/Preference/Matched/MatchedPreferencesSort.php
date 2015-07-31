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
     * Sort the array of MatchedPreferences in ascending order.
     *
     * @param MatchedPreferencesInterface[] $prefList
     *
     * @return MatchedPreferencesCollection
     */
    public function sortAscending(array $prefList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $prefList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return -1 * $comparator->compare($lValue, $rValue);
            }
        );

        $newCollection = new MatchedPreferencesCollection($this, $prefList);

        return $newCollection;
    }

    /**
     * Sort the array of MatchedPreferences in descending order.
     *
     * @param MatchedPreferencesInterface[] $prefList
     *
     * @return MatchedPreferencesCollection
     */
    public function sortDescending(array $prefList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $prefList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return $comparator->compare($lValue, $rValue);
            }
        );

        $newCollection = new MatchedPreferencesCollection($this, $prefList);

        return $newCollection;
    }

    /**
     * Get the best matching MatchedPreferences.
     *
     * @param MatchedPreferencesInterface[] $prefList
     *
     * @return MatchedPreferencesInterface
     */
    public function getBest(array $prefList)
    {
        $comparator = new MatchedPreferencesComparator();

        usort(
            $prefList,
            function (MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) use ($comparator) {
                return $comparator->compare($lValue, $rValue);
            }
        );

        if (count($prefList)) {
            $bestPair = $prefList[0];

        } else {
            $bestPair = $this->absentPreferences;
        }

        return $bestPair;
    }
}
