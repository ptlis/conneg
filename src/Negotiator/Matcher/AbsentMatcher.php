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

use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher creating MatchedPreferences with an absent application preference.
 */
class AbsentMatcher implements MatcherInterface
{
    /**
     * @var PreferenceBuilderInterface
     */
    private $preferenceBuilder;

    /**
     * @var PreferenceBuilderInterface
     */
    private $mimePreferenceBuilder;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $preferenceBuilder
     * @param PreferenceBuilderInterface $mimePreferenceBuilder
     */
    public function __construct(
        PreferenceBuilderInterface $preferenceBuilder,
        PreferenceBuilderInterface $mimePreferenceBuilder
    ) {
        $this->preferenceBuilder = $preferenceBuilder;
        $this->mimePreferenceBuilder = $mimePreferenceBuilder;
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
        $builder = $this->preferenceBuilder;
        if (PreferenceInterface::MIME === $userPreference->getFromField()) {
            $builder = $this->mimePreferenceBuilder;
        }

        $emptyPreference = $builder
            ->setFromField($userPreference->getFromField())
            ->get();

        $matchingList[] = new MatchedPreferences(
            $userPreference,
            $emptyPreference
        );

        return $matchingList;
    }
}
