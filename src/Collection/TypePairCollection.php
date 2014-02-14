<?php

/**
 * Collection for TypePair instances, provides sort capabilities.
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

namespace ptlis\ConNeg\Collection;

use ArrayIterator;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;
use Traversable;

/**
 * Collection for TypePair instances, provides sort capabilities.
 */
class TypePairCollection implements CollectionInterface
{
    /**
     * Used to prevent addition of application-provided types after user types have been set resulting in simpler logic.
     *
     * @var bool
     */
    private $userTypeSet = false;


    /**
     * @var TypePairInterface[]
     */
    private $typePairList;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->typePairList = array();
    }


    /**
     * Set the internal store to the provided values.
     *
     * @param TypePairInterface[] $typePairList
     *
     * @return CollectionInterface
     */
    public function setList(array $typePairList)
    {
        $this->typePairList = $typePairList;

        return $this;
    }


    /**
     * Add a type provided by the user-agent.
     *
     * @param TypeInterface $userType
     *
     * @return $this
     */
    public function addUserType(TypeInterface $userType)
    {
        $this->userTypeSet = true;

        if (!array_key_exists($userType->getFullType(), $this->typePairList)) {
            $this->typePairList[$userType->getFullType()] = new TypePair(null, $userType);

        } else {
            // Search for matching application type
            foreach ($this->typePairList as $typePair) {
                // Test for equality
                // Test for precedence
            }
        }

        return $this;
    }


    /**
     * Add a type provided by the application.
     *
     * @param TypeInterface $appType
     *
     * @return TypePairCollection
     */
    public function addAppType(TypeInterface $appType)
    {
        // Error if a user type has already been set.
        if ($this->userTypeSet) {
            // TODO: Throw exception
        }

        $this->typePairList[$appType->getFullType()] = new TypePair($appType, null);

        return $this;
    }


    /**
     * Return count of elements.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int
     */
    public function count()
    {
        return count($this->typePairList);
    }


    /**
     * Retrieve an external iterator.
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->typePairList);
    }


    /**
     * Returns a new sorted collection.
     *
     * @return TypePairCollection with elements in ascending order
     */
    public function getAscending()
    {
        // TODO: do we need to create a clone of the objects in here?
        $newTypePairList = $this->typePairList;

        $descSort = function (TypePairInterface $lTypePair, TypePairInterface $rTypePair) {
            if ($lTypePair->getQualityFactorProduct() < $rTypePair->getQualityFactorProduct()) {
                return -1;
            } elseif ($lTypePair->getQualityFactorProduct() === $rTypePair->getQualityFactorProduct()) {
                return 0;
            } else {
                return 1;
            }
        };

        usort(
            $newTypePairList,
            $descSort
        );

        $newCollection = new TypePairCollection();
        $newCollection->setList($newTypePairList);

        return $newCollection;
    }


    /**
     * Returns a new sorted collection.
     *
     * @return TypePairCollection with elements in descending order
     */
    public function getDescending()
    {
        // TODO: do we need to create a clone of the objects in here?
        $newTypePairList = $this->typePairList;

        $descSort = function (TypePairInterface $lTypePair, TypePairInterface $rTypePair) {
            if ($rTypePair->getQualityFactorProduct() < $lTypePair->getQualityFactorProduct()) {
                return -1;
            } elseif ($lTypePair->getQualityFactorProduct() === $rTypePair->getQualityFactorProduct()) {
                return 0;
            } else {
                return 1;
            }
        };

        usort(
            $newTypePairList,
            $descSort
        );

        $newCollection = new TypePairCollection();
        $newCollection->setList($newTypePairList);

        return $newCollection;
    }


    /**
     * Deep clone
     */
    public function __clone()
    {
        $newTypePairList = array();

        foreach ($this->typePairList as $index => $typePair) {
            $newTypePairList[$index] = clone $typePair;
        }

        $this->typePairList = $newTypePairList;
    }
}
