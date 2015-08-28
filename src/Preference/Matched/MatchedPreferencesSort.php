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
     * Sort the array of MatchedPreferences in ascending order.
     *
     * @param MatchedPreferencesInterface[] $prefList
     *
     * @return MatchedPreferencesInterface[]
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

        return $prefList;
    }

    /**
     * Sort the array of MatchedPreferences in descending order.
     *
     * @param MatchedPreferencesInterface[] $prefList
     *
     * @return MatchedPreferencesInterface[]
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

        return $prefList;
    }
}
