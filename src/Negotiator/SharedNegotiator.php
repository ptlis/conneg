<?php

/**
 * Class for negotiating on charset, encoding & language types.
 *
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
use ptlis\ConNeg\TypePair\SharedTypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Type\TypeFactoryInterface;
use ptlis\ConNeg\Type\WildcardType;

/**
 * Class for negotiating on charset, encoding & language types.
 */
class SharedNegotiator implements NegotiatorInterface
{
    /**
     * @var TypeFactoryInterface
     */
    private $typeFactory;


    /**
     * Constructor
     *
     * @param TypeFactoryInterface $typeFactory
     */
    public function __construct(TypeFactoryInterface $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }


    /**
     * Return a collection of types sorted by preference.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return SharedTypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new SharedTypePair(
                $appType,
                $this->typeFactory->get('', 0)
            );
        }

        $matchingList = $this->matchUserToAppTypes($userTypeList, $matchingList);

        $pairCollection = new SharedTypePairCollection();

        foreach ($matchingList as $matching) {
            $pairCollection->addPair($matching);
        }

        return $pairCollection->getDescending();
    }


    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
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
     * @param TypeCollection    $userTypeList
     * @param SharedTypePair[]    $matchingList
     *
     * @return SharedTypePair[]
     */
    private function matchUserToAppTypes(TypeCollection $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {

            // Type match
            if (array_key_exists($userType->getType(), $matchingList)) {
                $newPair = new SharedTypePair(
                    $matchingList[$userType->getType()]->getAppType(),
                    $userType
                );
                $sort = new TypePairSort();

                if ($sort->compare($matchingList[$userType->getType()], $newPair) > 0) {
                    $matchingList[$userType->getType()] = $newPair;
                }

            // Wildcard Match
            } elseif ($userType instanceof WildcardType) {
                $matchingList = $this->matchFullWildcard($matchingList, $userType);

            // No match
            } else {
                $matchingList[$userType->getType()] = new SharedTypePair(
                    $this->typeFactory->get('', 0),
                    $userType
                );
            }
        }

        return $matchingList;
    }


    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param SharedTypePair[]    $matchingList
     * @param TypeInterface $userType
     *
     * @return SharedTypePair[]
     */
    private function matchFullWildcard(array $matchingList, TypeInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new SharedTypePair(
                    $matchingList[$key]->getAppType(),
                    $userType
                );
            }
        }

        return $matchingList;
    }
}
