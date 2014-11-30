<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;
use ptlis\ConNeg\Type\MimeTypeInterface;
use ptlis\ConNeg\Type\MimeWildcardSubType;
use ptlis\ConNeg\Type\MimeWildcardType;

/**
 * Class for negotiating on mime types.
 */
class MimeNegotiator implements NegotiatorInterface
{
    /**
     * Empty type instance, used when only user & app types are asymmetric.
     *
     * @var TypeInterface
     */
    private $emptyType;

    /**
     * Instance of sorter than can reorder pairs.
     *
     * @var TypePairSort
     */
    private $pairSort;


    /**
     * Constructor.
     *
     * @param TypeInterface $emptyType
     * @param TypePairSort $pairSort
     */
    public function __construct(TypeInterface $emptyType, TypePairSort $pairSort)
    {
        $this->emptyType  = $emptyType;
        $this->pairSort     = $pairSort;
    }

    /**
     * Return a collection of types sorted by preference.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return TypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        /** @var MimeTypeInterface $appType */
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new TypePair(
                $this->emptyType,
                $appType
            );
        }

        $matchingList = $this->matchUserListToAppTypes($userTypeList, $matchingList);

        $pairList = array();
        foreach ($matchingList as $matching) {
            $pairList[] = $matching;
        }
        $pairCollection = new TypePairCollection($this->pairSort, $pairList);

        return $pairCollection->getDescending();
    }

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return TypePair
     */
    public function negotiateBest(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }

    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param TypePairInterface[]  $matchingList
     * @param MimeTypeInterface $userType
     *
     * @return TypePairInterface[]
     */
    private function matchFullWildcard(array $matchingList, MimeTypeInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new TypePair(
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
     * @param TypePairInterface[]   $matchingList
     * @param MimeTypeInterface     $userType
     *
     * @return TypePairInterface[]
     */
    private function matchSubTypeWildcard(array $matchingList, MimeTypeInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            /** @var MimeTypeInterface $appType */
            $appType = $matching->getAppType();
            if ($userType->getMimeType() == $appType->getMimeType()
                    && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {

                $matchingList[$key] = new TypePair(
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
     * @param TypePairInterface[]   $matchingList
     * @param MimeTypeInterface     $userType
     *
     * @return TypePairInterface[]
     */
    private function matchExact(array $matchingList, MimeTypeInterface $userType)
    {
        $newPair = new TypePair(
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
     * @param TypeCollection   $userTypeList
     * @param TypePairInterface[]  $matchingList
     *
     * @return TypePairInterface[]
     */
    private function matchUserListToAppTypes(TypeCollection $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {
            $matchingList = $this->matchUserToAppTypes($userType, $matchingList);
        }

        return $matchingList;
    }

    /**
     * Match a single user type to the application types.
     *
     * @param MimeTypeInterface     $userType
     * @param TypePairInterface[]   $matchingList
     *
     * @return TypePairInterface[]
     */
    private function matchUserToAppTypes(MimeTypeInterface $userType, array $matchingList)
    {
        switch (true) {
            // Full Wildcard Match
            case $userType instanceof MimeWildcardType:
                $matchingList = $this->matchFullWildcard($matchingList, $userType);
                break;

            // Wildcard SubType Match
            case $userType instanceof MimeWildcardSubType:
                $matchingList = $this->matchSubTypeWildcard($matchingList, $userType);
                break;

            // Exact Match
            case array_key_exists($userType->getType(), $matchingList):
                $matchingList = $this->matchExact($matchingList, $userType);
                break;

            // No match
            default:
                $matchingList[$userType->getType()] = new TypePair(
                    $userType,
                    $this->emptyType
                );
                break;
        }

        return $matchingList;
    }
}
