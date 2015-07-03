<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Preference\PreferenceCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class for negotiating on mime types.
 */
class MimeNegotiator implements NegotiatorInterface
{
    /**
     * Empty type instance, used when only user & app types are asymmetric.
     *
     * @var PreferenceInterface
     */
    private $emptyType;

    /**
     * Instance of sorter than can reorder pairs.
     *
     * @var MatchedPreferencesSort
     */
    private $pairSort;


    /**
     * Constructor.
     *
     * @param PreferenceInterface $emptyType
     * @param MatchedPreferencesSort $pairSort
     */
    public function __construct(PreferenceInterface $emptyType, MatchedPreferencesSort $pairSort)
    {
        $this->emptyType  = $emptyType;
        $this->pairSort     = $pairSort;
    }

    /**
     * Return a collection of types sorted by preference.
     *
     * @param PreferenceCollection $userTypeList
     * @param PreferenceCollection $appTypeList
     *
     * @return MatchedPreferencesCollection
     */
    public function negotiateAll(PreferenceCollection $userTypeList, PreferenceCollection $appTypeList)
    {
        $matchingList = array();
        /** @var PreferenceInterface $appType */
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new MatchedPreferences(
                $this->emptyType,
                $appType
            );
        }

        $matchingList = $this->matchUserListToAppTypes($userTypeList, $matchingList);
        $pairCollection = new MatchedPreferencesCollection($this->pairSort, $matchingList);

        return $pairCollection->getDescending();
    }

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param PreferenceCollection $userTypeList
     * @param PreferenceCollection $appTypeList
     *
     * @return MatchedPreferences
     */
    public function negotiateBest(PreferenceCollection $userTypeList, PreferenceCollection $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }

    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param MatchedPreferencesInterface[]  $matchingList
     * @param PreferenceInterface $userType
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchFullWildcard(array $matchingList, PreferenceInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new MatchedPreferences(
                    $userType,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }

    /**
     * Attempt to match wildcard subtypes against each item in matching list with an identical type.
     *
     * @param MatchedPreferencesInterface[]   $matchingList
     * @param PreferenceInterface     $userType
     *
     * @return MatchedPreferencesInterface[]
     */
    private function matchSubTypeWildcard(array $matchingList, PreferenceInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            $appType = $matching->getAppType();
            list($userMimeType) = explode('/', $userType->getType());
            list($appMimeType) = explode('/', $appType->getType());

            if ($userMimeType == $appMimeType
                    && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {

                $matchingList[$key] = new MatchedPreferences(
                    $userType,
                    $matchingList[$key]->getAppType()
                );
            }
        }

        return $matchingList;
    }

    /**
     * Attempt to match the given type to an existing type in typeList.
     *
     * @param MatchedPreferencesInterface[]   $matchingList
     * @param PreferenceInterface     $userType
     *
     * @return array<string,MatchedPreferencesInterface>
     */
    private function matchExact(array $matchingList, PreferenceInterface $userType)
    {
        $newPair = new MatchedPreferences(
            $userType,
            $matchingList[$userType->getType()]->getAppType()
        );

        if ($this->pairSort->compare($matchingList[$userType->getType()], $newPair) > 0) {
            $matchingList[$userType->getType()] = $newPair;
        }

        return $matchingList;
    }

    /**
     * Match user types to app types.
     *
     * @param PreferenceCollection   $userTypeList
     * @param MatchedPreferencesInterface[]  $matchingList
     *
     * @return  array<string,MatchedPreferencesInterface>
     */
    private function matchUserListToAppTypes(PreferenceCollection $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {
            $matchingList = $this->matchUserToAppTypes($userType, $matchingList);
        }

        return $matchingList;
    }

    /**
     * Match a single user type to the application types.
     *
     * @param PreferenceInterface     $userType
     * @param MatchedPreferencesInterface[]   $matchingList
     *
     * @return  array<string,MatchedPreferencesInterface>
     */
    private function matchUserToAppTypes(PreferenceInterface $userType, array $matchingList)
    {
        switch (true) {
            // Full Wildcard Match
            case Preference::WILDCARD === $userType->getPrecedence():
                $matchingList = $this->matchFullWildcard($matchingList, $userType);
                break;

            // Wildcard SubType Match
            case Preference::PARTIAL_WILDCARD === $userType->getPrecedence():
                $matchingList = $this->matchSubTypeWildcard($matchingList, $userType);
                break;

            // Exact Match
            case array_key_exists($userType->getType(), $matchingList):
                $matchingList = $this->matchExact($matchingList, $userType);
                break;

            // No match
            default:
                $matchingList[$userType->getType()] = new MatchedPreferences(
                    $userType,
                    $this->emptyType
                );
                break;
        }

        return $matchingList;
    }
}
