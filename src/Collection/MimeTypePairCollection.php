<?php

/**
 * Collection for MimeTypePair instances, provides sort capabilities.
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
use ptlis\ConNeg\Type\Mime\AbsentMimeType;
use ptlis\ConNeg\TypePair\MimeTypePair;
use Traversable;

/**
 * Collection for TypePair instances, provides sort capabilities.
 */
class MimeTypePairCollection implements CollectionInterface
{
    /**
     * @var MimeTypePair[]
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
     * @param MimeTypePair[] $typePairList
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
     * @param MimeTypePair $pair
     */
    public function addPair(MimeTypePair $pair)
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
     * @return MimeTypePairCollection with elements in ascending order
     */
    public function getAscending()
    {
        // TODO: do we need to create a clone of the objects in here?
        $newTypePairList = $this->typePairList;

        usort(
            $newTypePairList,
            $this->getAscendingSort()
        );

        $newCollection = new MimeTypePairCollection();
        $newCollection->setList($newTypePairList);

        return $newCollection;
    }


    /**
     * Returns a new sorted collection.
     *
     * @return MimeTypePairCollection with elements in descending order
     */
    public function getDescending()
    {
        // TODO: do we need to create a clone of the objects in here?
        $newTypePairList = $this->typePairList;

        usort(
            $newTypePairList,
            $this->getDescendingSort()
        );

        $newCollection = new MimeTypePairCollection();
        $newCollection->setList($newTypePairList);

        return $newCollection;
    }


    /**
     * Get the preferred pair.
     *
     * @return MimeTypePair
     */
    public function getBest()
    {
        // TODO: do we need to create a clone of the objects in here?
        $newTypePairList = $this->typePairList;

        usort(
            $newTypePairList,
            $this->getDescendingSort()
        );

        if (count($newTypePairList)) {
            $bestPair = $newTypePairList[0];

        } else {
            $bestPair = new MimeTypePair(
                new AbsentMimeType(),
                new AbsentMimeType()
            );
        }

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
     * Returns a callback that can be used with usort() to sort pairs into descending order.
     *
     * @return callable
     */
    private function getDescendingSort()
    {
        $sort = new TypePairSort();

        return function (MimeTypePair $lTypePair, MimeTypePair $rTypePair) use ($sort) {
            return $sort->compare($lTypePair, $rTypePair);
        };
    }


    /**
     * Returns a callback that can be used with usort() to sort pairs into ascending order.
     *
     * @return callable
     */
    public function getAscendingSort()
    {
        $sort = new TypePairSort();

        return function (MimeTypePair $lTypePair, MimeTypePair $rTypePair) use ($sort) {
            return -1 * $sort->compare($lTypePair, $rTypePair);
        };
    }
}
