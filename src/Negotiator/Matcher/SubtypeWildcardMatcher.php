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
    public function hasMatch(array $matchingList, PreferenceInterface $clientPref)
    {
        return PreferenceInterface::MIME === $clientPref->getFromField()
            && PreferenceInterface::PARTIAL_WILDCARD === $clientPref->getPrecedence();
    }

    /**
     * @inheritDoc
     */
    public function match(array $matchingList, PreferenceInterface $clientPref)
    {
        foreach ($matchingList as $key => $matching) {
            $serverPref = $matching->getServerPreference();
            list($clientMimeType) = explode('/', $clientPref->getType());
            list($serverMimeType) = explode('/', $serverPref->getType());

            if ($clientMimeType == $serverMimeType
                && $clientPref->getPrecedence() > $matching->getClientPreference()->getPrecedence()
            ) {
                $matchingList[$key] = new MatchedPreferences(
                    $clientPref,
                    $matchingList[$key]->getServerPreference()
                );
            }
        }

        return $matchingList;
    }
}
