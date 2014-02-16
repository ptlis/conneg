<?php

/**
 * Helper class encoding the rules governing the sorting of TypePairCollections.
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

use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\TypePair\TypePairInterface;

/**
 * Helper class encoding the rules governing the sorting of TypePairCollections.
 */
class TypePairSort
{
    /**
     *
     *
     * @param TypePairInterface $lTypePair
     * @param TypePairInterface $rTypePair
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    public function compare(TypePairInterface $lTypePair, TypePairInterface $rTypePair)
    {
        $lUserType = $lTypePair->getUserType();
        $rUserType = $rTypePair->getUserType();

        $lAppType = $lTypePair->getAppType();
        $rAppType = $rTypePair->getAppType();

        if (0 !== ($result = $this->compareQualityFactorProduct($lTypePair, $rTypePair))) {
            return $result;

        } elseif (0 !== ($result = $this->compareQualityFactor($lUserType, $rUserType))) {
            return $result;

        } elseif (0 !== ($result = $this->compareQualityFactor($lAppType, $rAppType))) {
            return $result;

        } else {
            return $this->compareType($lTypePair, $rTypePair);
        }
    }


    /**
     * @param TypePairInterface $lTypePair
     * @param TypePairInterface $rTypePair
     *
     * @return int -1, 0, 1 (see usort() callback for meaning)
     */
    private function compareQualityFactorProduct(TypePairInterface $lTypePair, TypePairInterface $rTypePair)
    {
        if ($rTypePair->getQualityFactorProduct() < $lTypePair->getQualityFactorProduct()) {
            return -1;
        } elseif ($rTypePair->getQualityFactorProduct() > $lTypePair->getQualityFactorProduct()) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
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
