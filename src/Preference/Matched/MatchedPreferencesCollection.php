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
    private $matchedPreferenceList;


    /**
     * Constructor.
     *
     * @param MatchedPreferencesSort $pairSort
     * @param MatchedPreferencesInterface[] $matchedPreferenceList
     */
    public function __construct(MatchedPreferencesSort $pairSort, array $matchedPreferenceList)
    {
        $this->pairSort     = $pairSort;
        $this->matchedPreferenceList = array_values($matchedPreferenceList);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->matchedPreferenceList);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->matchedPreferenceList);
    }

    /**
     * @inheritDoc
     */
    public function getAscending()
    {
        return $this->pairSort->sortAscending($this->matchedPreferenceList);
    }

    /**
     * @inheritDoc
     */
    public function getDescending()
    {
        return $this->pairSort->sortDescending($this->matchedPreferenceList);
    }

    /**
     * @inheritDoc
     */
    public function getBest()
    {
        $bestPair = $this->pairSort->getBest($this->matchedPreferenceList);

        return $bestPair;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return implode(',', $this->matchedPreferenceList);
    }
}
