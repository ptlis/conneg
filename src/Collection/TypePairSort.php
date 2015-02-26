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

namespace ptlis\ConNeg\Collection;

use ptlis\ConNeg\TypePair\TypePairInterface;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Helper class encoding the rules governing the sorting of TypePairCollections.
 */
class TypePairSort
{
    /**
     * Default type pair returned by 'best' negotiation when a collection is empty.
     *
     * @var TypePairInterface
     */
    private $defaultPair;


    /**
     * Constructor.
     *
     * @param TypePairInterface $defaultPair    Default type pair used for 'best' negotiation where the collection is
     *                                          empty.
     */
    public function __construct(TypePairInterface $defaultPair)
    {
        $this->defaultPair = $defaultPair;
    }

    /**
     * Sort the array of typePairs in ascending order.
     *
     * @param TypePairInterface[] $typePairList
     *
     * @return TypePairCollection
     */
    public function sortAscending(array $typePairList)
    {
        $that = $this;
        usort(
            $typePairList,
            function (TypePairInterface $lTypePair, TypePairInterface $rTypePair) use ($that) {
                return -1 * $that->compare($lTypePair, $rTypePair);
            }
        );

        $newCollection = new TypePairCollection($this, $typePairList);

        return $newCollection;
    }

    /**
     * Sort the array of typePairs in descending order.
     *
     * @param TypePairInterface[] $typePairList
     *
     * @return TypePairCollection
     */
    public function sortDescending(array $typePairList)
    {
        $that = $this;
        usort(
            $typePairList,
            function (TypePairInterface $lTypePair, TypePairInterface $rTypePair) use ($that) {
                return $that->compare($lTypePair, $rTypePair);
            }
        );

        $newCollection = new TypePairCollection($this, $typePairList);

        return $newCollection;
    }

    /**
     * Get the best matching type pair.
     *
     * @param TypePairInterface[] $typePairList
     *
     * @return TypePairInterface
     */
    public function getBest(array $typePairList)
    {
        $that = $this;
        usort(
            $typePairList,
            function (TypePairInterface $lTypePair, TypePairInterface $rTypePair) use ($that) {
                return $that->compare($lTypePair, $rTypePair);
            }
        );

        if (count($typePairList)) {
            $bestPair = $typePairList[0];

        } else {
            $bestPair = $this->defaultPair;
        }

        return $bestPair;
    }

    /**
     * Comparison function used for ordering type pairs.
     *
     * @param TypePairInterface $lTypePair
     * @param TypePairInterface $rTypePair
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    public function compare(TypePairInterface $lTypePair, TypePairInterface $rTypePair)
    {
        // Compare by quality factors - highest quality factor has precedence.
        $result = $this->compareQualityFactorPair($lTypePair, $rTypePair);

        // If we did not get a preferred result from the above check then simply sort alphabetically - this ensures that
        // the sort is always stable.
        if (0 === $result) {
            $result = $this->compareType($lTypePair, $rTypePair);
        }

        return $result;
    }

    /**
     * Comparison function for quality factors of type pairs.
     *
     * @param TypePairInterface $lTypePair
     * @param TypePairInterface $rTypePair
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactorPair(TypePairInterface $lTypePair, TypePairInterface $rTypePair)
    {
        // Build a list of quality factor comparisons to perform; highest preference given to quality factor products,
        // followed by those provided by the user agent & finally the application provided.
        $compareList = array(
            array(
                'left' => $lTypePair,
                'right' => $rTypePair
            ),
            array(
                'left' => $lTypePair->getUserType(),
                'right' => $rTypePair->getUserType()
            ),
            array(
                'left' => $lTypePair->getAppType(),
                'right' => $rTypePair->getAppType()
            )
        );

        $result = 0;
        foreach ($compareList as $compare) {
            $result = $this->compareQualityFactor($compare['left'], $compare['right']);

            // If a non matching result was found then we have the result of our comparison
            if (0 !== $result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Compare quality factors of types.
     *
     * @param TypeInterface $lType
     * @param TypeInterface $rType
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactor(TypeInterface $lType, TypeInterface $rType)
    {
        if ($rType->getQualityFactor() < $lType->getQualityFactor()) {
            return -1;
        } elseif ($rType->getQualityFactor() > $lType->getQualityFactor()) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Compare types alphabetically
     *
     * @param TypePairInterface $lTypePair
     * @param TypePairInterface $rTypePair
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareType(TypePairInterface $lTypePair, TypePairInterface $rTypePair)
    {
        return strcasecmp($lTypePair->getType(), $rTypePair->getType());
    }
}
