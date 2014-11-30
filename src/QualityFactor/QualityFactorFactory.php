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

namespace ptlis\ConNeg\QualityFactor;

use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Exception\QualityFactorMalformedException;
use ptlis\ConNeg\Exception\QualityFactorNegativeException;
use ptlis\ConNeg\Exception\QualityFactorTooLargeException;

/**
 * Factory class creating QualityFactor instances.
 */
class QualityFactorFactory
{
    /**
     * Create a new QualityFactor instances with the provided quality factor
     *
     * @throws ConNegException
     *
     * @param int|double $qualityFactor
     * @param bool $appType
     *
     * @return QualityFactor
     */
    public function get($qualityFactor, $appType)
    {
        if (!strlen($qualityFactor)) {
            $qualityFactor = 1;
        } else {
            $qualityFactor = $this->validateFloat($qualityFactor, $appType);
            $qualityFactor = $this->validateUpper($qualityFactor, $appType);
            $qualityFactor = $this->validateLower($qualityFactor, $appType);
        }

        return new QualityFactor($qualityFactor);
    }

    /**
     * Check to see if the quality factor is a valid float.
     *
     * @throws QualityFactorMalformedException
     *
     * @param int|double $qualityFactor
     * @param bool $appType
     *
     * @return int|double
     */
    private function validateFloat($qualityFactor, $appType)
    {
        $normalisedQFactor = filter_var($qualityFactor, FILTER_VALIDATE_FLOAT);

        if (false === $normalisedQFactor) {
            if ($appType) {
                throw new QualityFactorMalformedException(
                    'Invalid quality factor of "' . $qualityFactor . '" provided, must be between 0 and 1 (inclusive)'
                );
            } else {
                $normalisedQFactor = 1;
            }
        }

        return $normalisedQFactor;
    }

    /**
     * Check to see if the quality factor is greater than 1.
     *
     * @throws QualityFactorTooLargeException
     *
     * @param int|double $qualityFactor
     * @param bool $appType
     *
     * @return int|double
     */
    private function validateUpper($qualityFactor, $appType)
    {
        if ($qualityFactor > 1) {
            if ($appType) {
                throw new QualityFactorTooLargeException(
                    'Invalid quality factor of "' . $qualityFactor . '" provided, must be between 0 and 1 (inclusive)'
                );
            } else {
                $qualityFactor = 1;
            }
        }

        return $qualityFactor;
    }

    /**
     * Check to see if the quality factor is less than 0.
     *
     * @throws QualityFactorNegativeException
     *
     * @param int|double $qualityFactor
     * @param bool $appType
     *
     * @return int|double
     */
    private function validateLower($qualityFactor, $appType)
    {
        if ($qualityFactor < 0) {
            if ($appType) {
                throw new QualityFactorNegativeException(
                    'Invalid quality factor of "' . $qualityFactor . '" provided, must be between 0 and 1 (inclusive)'
                );
            } else {
                $qualityFactor = 0;
            }
        }

        return $qualityFactor;
    }
}
