<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
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
    public function sortAscending(array $prefList): array
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
    public function sortDescending(array $prefList): array
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
