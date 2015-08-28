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
use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher creating MatchedPreference instances with an absent server preference.
 */
class AbsentMatcher implements MatcherInterface
{
    /**
     * @var PreferenceBuilderInterface
     */
    private $prefBuilder;

    /**
     * @var PreferenceBuilderInterface
     */
    private $mimePrefBuilder;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $prefBuilder
     * @param PreferenceBuilderInterface $mimePrefBuilder
     */
    public function __construct(
        PreferenceBuilderInterface $prefBuilder,
        PreferenceBuilderInterface $mimePrefBuilder
    ) {
        $this->prefBuilder = $prefBuilder;
        $this->mimePrefBuilder = $mimePrefBuilder;
    }

    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        return true; // Claim to always match
    }

    /**
     * @inheritDoc
     */
    public function match(array $matchingList, PreferenceInterface $clientPref)
    {
        $builder = $this->prefBuilder;
        if (PreferenceInterface::MIME === $clientPref->getFromField()) {
            $builder = $this->mimePrefBuilder;
        }

        $emptyPref = $builder
            ->setFromField($clientPref->getFromField())
            ->get();

        $matchingList[] = new MatchedPreference(
            $clientPref,
            $emptyPref
        );

        return $matchingList;
    }
}
