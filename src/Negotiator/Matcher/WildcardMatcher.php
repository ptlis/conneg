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
 * Matcher for full wildcards (e.g. '*' and '*\/*
 */
class WildcardMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        // TODO: Check to see if there is an actual match?
        return PreferenceInterface::WILDCARD === $userPreference->getPrecedence();
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userPreference->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new MatchedPreferences(
                    $userPreference,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }
}
