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

use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Type\WildcardType;

/**
 * Class for negotiating on charset, encoding & language types.
 */
class Negotiator implements NegotiatorInterface
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
     * Constructor
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
     * @param TypeCollection|TypeInterface[] $userTypeList
     * @param TypeCollection|TypeInterface[] $appTypeList
     *
     * @return SharedTypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new TypePair(
                $this->emptyType,
                $appType
            );
        }

        $matchingList = $this->matchUserToAppTypes($userTypeList, $matchingList);

        $pairCollection = new SharedTypePairCollection($this->pairSort);

        foreach ($matchingList as $matching) {
            $pairCollection->addPair($matching);
        }

        return $pairCollection->getDescending();
    }

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param TypeCollection|TypeInterface[] $userTypeList
     * @param TypeCollection|TypeInterface[] $appTypeList
     *
     * @return TypePairInterface
     */
    public function negotiateBest(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }

    /**
     * Match user types to app types.
     *
     * @param TypeCollection|TypeInterface[]    $userTypeList
     * @param TypePair[]                  $matchingList
     *
     * @return TypePair[]
     */
    private function matchUserToAppTypes(TypeCollection $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {

            // Type match
            if (array_key_exists($userType->getType(), $matchingList)) {
                $newPair = new TypePair(
                    $userType,
                    $matchingList[$userType->getType()]->getAppType()
                );

                if ($this->pairSort->compare($matchingList[$userType->getType()], $newPair) > 0) {
                    $matchingList[$userType->getType()] = $newPair;
                }

            // Wildcard Match
            } elseif ($userType instanceof WildcardType) {
                $matchingList = $this->matchFullWildcard($matchingList, $userType);

            // No match
            } else {
                $matchingList[$userType->getType()] = new TypePair(
                    $userType,
                    $this->emptyType
                );
            }
        }

        return $matchingList;
    }

    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param TypePair[]    $matchingList
     * @param TypeInterface $userType
     *
     * @return TypePair[]
     */
    private function matchFullWildcard(array $matchingList, TypeInterface $userType)
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
}
