<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Negotiator\Matcher;

use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Matcher creating MatchedPreference instances with an absent server preference.
 */
final class AbsentMatcher implements MatcherInterface
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
    public function hasMatch(string $fromField, array $matchingList, PreferenceInterface $clientPref): bool
    {
        return true; // Claim to always match
    }

    /**
     * @inheritDoc
     */
    public function match(string $fromField, array $matchingList, PreferenceInterface $clientPref): array
    {
        $builder = $this->prefBuilder;
        if (PreferenceInterface::MIME === $fromField) {
            $builder = $this->mimePrefBuilder;
        }

        $emptyPref = $builder
            ->setFromField($fromField)
            ->get();

        $matchingList[] = new MatchedPreference(
            $fromField,
            $clientPref,
            $emptyPref
        );

        return $matchingList;
    }
}
