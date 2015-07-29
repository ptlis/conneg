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

/**
 * Collection for MatchedPreferences instances, provides sort capabilities.
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
     * Array of MatchedPreferences contained within this collection.
     *
     * @var MatchedPreferencesInterface[]
     */
    private $matchedPrefList;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesSort $pairSort
     * @param MatchedPreferencesInterface[] $matchedPrefList
     */
    public function __construct(MatchedPreferencesSort $pairSort, array $matchedPrefList)
    {
        $this->pairSort     = $pairSort;
        $this->matchedPrefList = array_values($matchedPrefList);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->matchedPrefList);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->matchedPrefList);
    }

    /**
     * @inheritDoc
     */
    public function getAscending()
    {
        return $this->pairSort->sortAscending($this->matchedPrefList);
    }

    /**
     * @inheritDoc
     */
    public function getDescending()
    {
        return $this->pairSort->sortDescending($this->matchedPrefList);
    }

    /**
     * @inheritDoc
     */
    public function getBest()
    {
        $bestPair = $this->pairSort->getBest($this->matchedPrefList);

        return $bestPair;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return implode(',', $this->matchedPrefList);
    }
}
