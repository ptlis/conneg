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

namespace ptlis\ConNeg\Collection;

use ArrayIterator;
use Traversable;
use ptlis\ConNeg\TypePair\TypePairInterface;

/**
 * Collection for TypePair instances, provides sort capabilities.
 */
class SharedTypePairCollection implements CollectionInterface
{
    /**
     * Instance of sorter than can reorder pairs.
     *
     * @var TypePairSort
     */
    private $pairSort;

    /**
     * Type Pairs contained within this collection.
     *
     * @var TypePairInterface[]
     */
    private $typePairList;


    /**
     * Constructor.
     *
     * @param TypePairSort $pairSort
     * @param TypePairInterface[] $typePairList
     */
    public function __construct(TypePairSort $pairSort, array $typePairList)
    {
        $this->pairSort     = $pairSort;
        $this->typePairList = $typePairList;
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
        return $this->pairSort->sortAscending($this->typePairList);
    }

    /**
     * Returns a new sorted collection.
     *
     * @return SharedTypePairCollection with elements in descending order
     */
    public function getDescending()
    {
        return $this->pairSort->sortDescending($this->typePairList);
    }

    /**
     * Get the preferred pair.
     *
     * @return TypePairInterface
     */
    public function getBest()
    {
        $bestPair = $this->pairSort->getBest($this->typePairList);

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

    /**
     * Return a string representation of the collection.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(',', $this->typePairList);
    }
}
