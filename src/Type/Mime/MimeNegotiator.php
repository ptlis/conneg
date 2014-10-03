<?php

/**
 * Class for negotiating on mime types.
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

namespace ptlis\ConNeg\Type\Mime;

use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Type\Mime\Interfaces\MimeTypeInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\NegotiatorInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface;
use ptlis\ConNeg\TypePair\SharedTypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;

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
     * @param TypeCollection|TypeInterface[] $userTypeList
     * @param TypeCollection|TypeInterface[] $appTypeList
     *
     * @return SharedTypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = new SharedTypePair(
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
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return SharedTypePair
     */
    public function negotiateBest(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }


    /**
     * Attempt to match wildcard type against each item in matching list.
     *
     * @param SharedTypePair[]  $matchingList
     * @param MimeTypeInterface $userType
     *
     * @return SharedTypePair[]
     */
    private function matchFullWildcard(array $matchingList, MimeTypeInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {
                $matchingList[$key] = new SharedTypePair(
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
     * @return SharedTypePair[]
     */
    private function matchSubTypeWildcard(array $matchingList, MimeTypeInterface $userType)
    {
        foreach ($matchingList as $key => $matching) {
            if ($userType->getMimeType() == $matching->getAppType()->getMimeType()
                    && $userType->getPrecedence() > $matching->getUserType()->getPrecedence()) {

                $matchingList[$key] = new SharedTypePair(
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
        $newPair = new SharedTypePair(
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
     * @param TypeCollection|TypeInterface[]   $userTypeList
     * @param SharedTypePair[]  $matchingList
     *
     * @return SharedTypePair[]
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
                $matchingList[$userType->getType()] = new SharedTypePair(
                    $userType,
                    $this->typeFactory->get('', 0)
                );
            }
        }

        return $matchingList;
    }
}
