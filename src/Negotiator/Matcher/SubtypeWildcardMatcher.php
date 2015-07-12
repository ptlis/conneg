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
 * Matcher for subtype wildcards (e.g. 'text/*').
 */
class SubtypeWildcardMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function hasMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        return PreferenceInterface::MIME === $userPreference->getFromField()
            && PreferenceInterface::PARTIAL_WILDCARD === $userPreference->getPrecedence();
    }

    /**
     * @inheritDoc
     */
    public function doMatch(array $matchingList, PreferenceInterface $userPreference)
    {
        foreach ($matchingList as $key => $matching) {
            $appPreference = $matching->getAppType();
            list($userMimeType) = explode('/', $userPreference->getType());
            list($appMimeType) = explode('/', $appPreference->getType());

            if ($userMimeType == $appMimeType
                && $userPreference->getPrecedence() > $matching->getUserType()->getPrecedence()) {

                $matchingList[$key] = new MatchedPreferences(
                    $userPreference,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }
}
