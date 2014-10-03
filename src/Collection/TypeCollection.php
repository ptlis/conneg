<?php

/**
 * Collection for Type instances, provides sort capabilities.
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
use Traversable;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Collection for Type instances, provides sort capabilities.
 */
class TypeCollection implements CollectionInterface
{
    /**
     * @var TypeInterface[]
     */
    private $typeList;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->typeList = array();
    }


    /**
     * Set the internal store to the provided values.
     *
     * @param TypeInterface[] $typeList
     *
     * @return CollectionInterface
     */
    public function setList(array $typeList)
    {
        foreach ($typeList as $type) {
            $this->addType($type);
        }

        return $this;
    }


    /**
     * Add a type to the collection.
     *
     * @param TypeInterface $type
     *
     * @return $this
     */
    public function addType(TypeInterface $type)
    {
        $this->typeList[] = $type;

        return $this;
    }


    /**
     * Return count of elements.
     *
     * @return int
     */
    public function count()
    {
        return count($this->typeList);
    }


    /**
     * Retrieve an external iterator.
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->typeList);
    }


    /**
     * Returns a new sorted collection.
     *
     * @return TypeCollection with elements in ascending order
     */
    public function getAscending()
    {
        $newTypeList = $this->typeList;

        $descSort = function (TypeInterface $lTypePair, TypeInterface $rTypePair) {
            if ($lTypePair->getQualityFactor() < $rTypePair->getQualityFactor()) {
                return -1;
            } elseif ($lTypePair->getQualityFactor()->getFactor() === $rTypePair->getQualityFactor()->getFactor()) {
                return strcasecmp($lTypePair->getType(), $rTypePair->getType());
            } else {
                return 1;
            }
        };

        usort(
            $newTypeList,
            $descSort
        );

        $newCollection = new TypeCollection();
        $newCollection->setList($newTypeList);

        return $newCollection;
    }


    /**
     * Returns a new sorted collection.
     *
     * @return TypeCollection with elements in descending order
     */
    public function getDescending()
    {
        $newTypePairList = $this->typeList;

        $descSort = function (TypeInterface $lTypePair, TypeInterface $rTypePair) {
            if ($rTypePair->getQualityFactor() < $lTypePair->getQualityFactor()) {
                return -1;
            } elseif ($lTypePair->getQualityFactor()->getFactor() === $rTypePair->getQualityFactor()->getFactor()) {
                return strcasecmp($lTypePair->getType(), $rTypePair->getType());
            } else {
                return 1;
            }
        };

        usort(
            $newTypePairList,
            $descSort
        );

        $newCollection = new TypeCollection();
        $newCollection->setList($newTypePairList);

        return $newCollection;
    }


    /**
     * Return a string representation of the collection.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(',', $this->typeList);
    }
}