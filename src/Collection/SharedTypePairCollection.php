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
use Traversable;
use ptlis\ConNeg\TypePair\SharedTypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;
use ptlis\ConNeg\Type\AbsentType;

/**
 * Collection for TypePair instances, provides sort capabilities.
 */
class SharedTypePairCollection implements CollectionInterface
{
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
     * Add a type pair to the collection.
     *
     * @param TypePairInterface $pair
     */
    public function addPair(TypePairInterface $pair)
    {
        $this->typePairList[] = $pair;
    }


    /**
     * Return count of elements.
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
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->typePairList);
    }


    /**
     * Returns a new sorted collection.
     *
     * @return SharedTypePairCollection with elements in ascending order
     */
    public function getAscending()
    {
        $newCollection = new SharedTypePairCollection();
        $sort = new TypePairSort();
        $sort->sortAscending($this->typePairList, $newCollection);

        return $newCollection;
    }


    /**
     * Returns a new sorted collection.
     *
     * @return SharedTypePairCollection with elements in descending order
     */
    public function getDescending()
    {
        $newCollection = new SharedTypePairCollection();
        $sort = new TypePairSort();
        $sort->sortDescending($this->typePairList, $newCollection);

        return $newCollection;
    }


    /**
     * Get the preferred pair.
     *
     * @return TypePairInterface
     */
    public function getBest()
    {
        $defaultPair = new SharedTypePair(
            new AbsentType(),
            new AbsentType()
        );
        $sort = new TypePairSort();
        $bestPair = $sort->getBest($this->typePairList, $defaultPair);

        return $bestPair;
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
