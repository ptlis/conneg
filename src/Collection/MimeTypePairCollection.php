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
use Traversable;
use ptlis\ConNeg\TypePair\MimeTypePair;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;

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
        $newCollection = new MimeTypePairCollection();
        $sort = new TypePairSort();
        $sort->sortAscending($this->typePairList, $newCollection);

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
        $newCollection = new MimeTypePairCollection();
        $sort = new TypePairSort();
        $sort->sortDescending($this->typePairList, $newCollection);

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
        $defaultPair = new MimeTypePair(
            new AbsentMimeType(),
            new AbsentMimeType()
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
