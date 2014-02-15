<?php

/**
 * Interface that collections must implement.
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

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Interface that collections must implement.
 */
interface CollectionInterface extends Countable, IteratorAggregate
{
    /**
     * Set the internal store to the provided values.
     *
     * @param array $list
     *
     * @return CollectionInterface
     */
    public function setList(array $list);


    /**
     * Return count of elements.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int
     */
    public function count();


    /**
     * Retrieve an external iterator.
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable
     */
    public function getIterator();


    /**
     * Returns a new sorted collection.
     *
     * @return CollectionInterface with elements in ascending order
     */
    public function getAscending();


    /**
     * Returns a new sorted collection.
     *
     * @return CollectionInterface with elements in descending order
     */
    public function getDescending();
}
