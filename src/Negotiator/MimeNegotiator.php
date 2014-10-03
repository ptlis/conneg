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
use ptlis\ConNeg\TypeFactory\MimeTypeFactory;
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
     * @var MimeTypeFactory
     */
    private $typeFactory;

    /**
     * @var TypePairSort
     */
    private $pairSort;


    /**
     * Constructor.
     *
     * @param MimeTypeFactory $typeFactory
     * @param TypePairSort    $pairSort
     */
    public function __construct(MimeTypeFactory $typeFactory, TypePairSort $pairSort)
    {
        $this->typeFactory  = $typeFactory;
        $this->pairSort     = $pairSort;
    }

    /**
     * Return a collection of types sorted by preference.
     *
     * @param TypeCollection|MimeTypeInterface[] $userTypeList
     * @param TypeCollection|MimeTypeInterface[] $appTypeList
     *
     * @return SharedTypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new TypePair(
                $this->typeFactory->get('', 0),
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
     * @param TypeCollection|MimeTypeInterface[] $userTypeList
     * @param TypeCollection|MimeTypeInterface[] $appTypeList
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
     * @param TypePair[]  $matchingList
     * @param MimeTypeInterface $userType
     *
     * @return TypePair[]
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
     * @return TypePair[]
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
     * @return array
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
     * @param TypeCollection|MimeTypeInterface[]   $userTypeList
     * @param TypePair[]  $matchingList
     *
     * @return TypePair[]
     */
    private function matchUserToAppTypes(TypeCollection $userTypeList, array $matchingList)
    {
        foreach ($userTypeList as $userType) {

            // Full Wildcard Match
            if ($userType instanceof MimeWildcardType) {
                $matchingList = $this->matchFullWildcard($matchingList, $userType);

            // Wildcard SubType Match
            } elseif ($userType instanceof MimeWildcardSubType) {
                $matchingList = $this->matchSubTypeWildcard($matchingList, $userType);

            // Exact Match
            } elseif (array_key_exists($userType->getType(), $matchingList)) {
                $matchingList = $this->matchExact($matchingList, $userType);

            // No match
            } else {
                $matchingList[$userType->getType()] = new TypePair(
                    $userType,
                    $this->typeFactory->get('', 0)
                );
            }
        }

        return $matchingList;
    }
}
