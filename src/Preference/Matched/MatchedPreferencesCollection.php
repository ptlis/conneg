<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Preference\Matched;

use ArrayIterator;
use ptlis\ConNeg\Preference\CollectionInterface;

/**
 * Collection for TypePair instances, provides sort capabilities.
 */
class MatchedPreferencesCollection implements CollectionInterface
{
    /**
     * Instance of sorter than can reorder pairs.
     *
     * @var MatchedPreferencesSort
     */
    private $pairSort;

    /**
     * Type Pairs contained within this collection.
     *
     * @var MatchedPreferencesInterface[]
     */
    private $typePairList;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesSort $pairSort
     * @param MatchedPreferencesInterface[] $typePairList
     */
    public function __construct(MatchedPreferencesSort $pairSort, array $typePairList)
    {
        $this->pairSort     = $pairSort;
        $this->typePairList = array_values($typePairList);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->typePairList);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->typePairList);
    }

    /**
     * @inheritDoc
     */
    public function getAscending()
    {
        return $this->pairSort->sortAscending($this->typePairList);
    }

    /**
     * @inheritDoc
     */
    public function getDescending()
    {
        return $this->pairSort->sortDescending($this->typePairList);
    }

    /**
     * @inheritDoc
     */
    public function getBest()
    {
        $bestPair = $this->pairSort->getBest($this->typePairList);

        return $bestPair;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return implode(',', $this->typePairList);
    }
}
