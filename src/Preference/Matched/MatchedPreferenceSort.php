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
class MatchedPreferenceSort
{
    /**
     * Sort the array of MatchedPreference instances in ascending order.
     *
     * @param MatchedPreferenceInterface[] $prefList
     *
     * @return MatchedPreferenceInterface[]
     */
    public function sortAscending(array $prefList)
    {
        $comparator = new MatchedPreferenceComparator();

        usort(
            $prefList,
            function (MatchedPreferenceInterface $lValue, MatchedPreferenceInterface $rValue) use ($comparator) {
                return -1 * $comparator->compare($lValue, $rValue);
            }
        );

        return $prefList;
    }

    /**
     * Sort the array of MatchedPreference instances in descending order.
     *
     * @param MatchedPreferenceInterface[] $prefList
     *
     * @return MatchedPreferenceInterface[]
     */
    public function sortDescending(array $prefList)
    {
        $comparator = new MatchedPreferenceComparator();

        usort(
            $prefList,
            function (MatchedPreferenceInterface $lValue, MatchedPreferenceInterface $rValue) use ($comparator) {
                return $comparator->compare($lValue, $rValue);
            }
        );

        return $prefList;
    }
}
