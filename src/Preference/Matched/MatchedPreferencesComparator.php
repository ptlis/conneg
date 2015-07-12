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

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Comparator used to order the type preferences.
 */
class MatchedPreferencesComparator
{
    /**
     * Comparison function used for ordering matched preferences.
     *
     * @param MatchedPreferencesInterface $lValue
     * @param MatchedPreferencesInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    public function compare(MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) {
        // Compare by quality factors - highest quality factor has precedence.
        $result = $this->compareQualityFactorPair($lValue, $rValue);

        // Quality factors are equal attempt to sort by match precedence
        if (0 === $result) {
            $result = $this->comparePrecedence($lValue, $rValue);
        }

        // Quality factors & precedences match, simply sort alphabetically as this ensures that the sort is stable
        if (0 === $result) {
            $result = $this->compareType($lValue, $rValue);
        }

        return $result;
    }

    /**
     * Comparison function for quality factors of matched preferences.
     *
     * @param MatchedPreferencesInterface $lValue
     * @param MatchedPreferencesInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactorPair(MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue) {
        // Build a list of quality factor comparisons to perform; highest preference given to quality factor products,
        // followed by those provided by the user agent & finally the application provided.
        $compareList = array(
            array(
                'left' => $lValue,
                'right' => $rValue
            ),
            array(
                'left' => $lValue->getUserPreference(),
                'right' => $rValue->getUserPreference()
            ),
            array(
                'left' => $lValue->getAppPreference(),
                'right' => $rValue->getAppPreference()
            )
        );

        $result = 0;
        foreach ($compareList as $compare) {
            $result = $this->compareQualityFactor($compare['left'], $compare['right']);

            // If a non matching result was found then we have the result of our comparison
            if (0 !== $result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Compare quality factors of types.
     *
     * @param PreferenceInterface $lValue
     * @param PreferenceInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactor(PreferenceInterface $lValue, PreferenceInterface $rValue)
    {
        if ($rValue->getQualityFactor() < $lValue->getQualityFactor()) {
            return -1;
        } elseif ($rValue->getQualityFactor() > $lValue->getQualityFactor()) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Compare precedences of types.
     *
     * @param PreferenceInterface $lValue
     * @param PreferenceInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function comparePrecedence(PreferenceInterface $lValue, PreferenceInterface $rValue)
    {
        if ($rValue->getPrecedence() < $lValue->getPrecedence()) {
            return -1;
        } elseif ($rValue->getPrecedence() > $lValue->getPrecedence()) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Compare types alphabetically
     *
     * @param MatchedPreferencesInterface $lValue
     * @param MatchedPreferencesInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareType(MatchedPreferencesInterface $lValue, MatchedPreferencesInterface $rValue)
    {
        return strcasecmp($lValue->getType(), $rValue->getType());
    }
}
