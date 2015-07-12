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
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher creating MatchedPreferences with an absent application preference.
 */
class AbsentMatcher implements MatcherInterface
{
    /**
     * @var PreferenceInterface
     */
    private $emptyPreference;

    /**
     * @var PreferenceInterface
     */
    private $mimeEmptyPreference;


    /**
     * Constructor.
     *
     * @param PreferenceInterface $emptyPreference
     * @param PreferenceInterface $mimeEmptyPreference
     */
    public function __construct(PreferenceInterface $emptyPreference, PreferenceInterface $mimeEmptyPreference)
    {
        $this->emptyPreference = $emptyPreference;
        $this->mimeEmptyPreference = $mimeEmptyPreference;
    }

    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        return true; // Claim to always match
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        $emptyPreference = $this->emptyPreference;
        if (PreferenceInterface::MIME === $userPreference->getFromField()) {
            $emptyPreference = $this->mimeEmptyPreference;
        }

        $matchingList[$userPreference->getType()] = new MatchedPreferences(
            $userPreference,
            $emptyPreference
        );

        return $matchingList;
    }
}
