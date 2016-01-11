<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Comparator used to order the preferences.
 */
class MatchedPreferenceComparator
{
    /**
     * Comparison function used for ordering matched preferences.
     *
     * @param MatchedPreferenceInterface $lValue
     * @param MatchedPreferenceInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    public function compare(MatchedPreferenceInterface $lValue, MatchedPreferenceInterface $rValue)
    {
        // Compare by quality factors - highest quality factor has precedence.
        $result = $this->compareQualityFactorPair($lValue, $rValue);

        // Quality factors are equal attempt to sort by match precedence
        if (0 === $result) {
            $result = $this->comparePrecedence($lValue, $rValue);
        }

        // Quality factors & precedences match, simply sort alphabetically as this ensures that the sort is stable
        if (0 === $result) {
            $result = $this->compareVariant($lValue, $rValue);
        }

        return $result;
    }

    /**
     * Comparison function for quality factors of matched preferences.
     *
     * @param MatchedPreferenceInterface $lValue
     * @param MatchedPreferenceInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactorPair(
        MatchedPreferenceInterface $lValue,
        MatchedPreferenceInterface $rValue
    ) {
        // Build a list of quality factor comparisons to perform; highest preference given to quality factor products,
        // followed by those provided by the client & finally the server provided.
        $compareList = array(
            array(
                'left' => $lValue,
                'right' => $rValue
            ),
            array(
                'left' => $lValue->getClientPreference(),
                'right' => $rValue->getClientPreference()
            ),
            array(
                'left' => $lValue->getServerPreference(),
                'right' => $rValue->getServerPreference()
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
     * Compare quality factors of preferences.
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
     * Compare precedences of preferences.
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
     * Compare preferences alphabetically
     *
     * @param MatchedPreferenceInterface $lValue
     * @param MatchedPreferenceInterface $rValue
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareVariant(MatchedPreferenceInterface $lValue, MatchedPreferenceInterface $rValue)
    {
        return strcasecmp($lValue->getVariant(), $rValue->getVariant());
    }
}
