<?php

/**
 * Factory class creating QualityFactor instances.
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

namespace ptlis\ConNeg\QualityFactor;

use ptlis\ConNeg\Exception\ConNegException;

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
     * @param float $qualityFactor
     *
     * @return QualityFactor
     */
    public function get($qualityFactor)
    {
        if (!strlen($qualityFactor)) {
            $qualityFactor = 1;
        } elseif (false === ($qualityFactor = filter_var($qualityFactor, FILTER_VALIDATE_FLOAT))) {
            throw new ConNegException(
                'Invalid quality factor of "' . $qualityFactor . '" provided'
            );
        } elseif ($qualityFactor < 0 || $qualityFactor > 1) {
            throw new ConNegException(
                'Invalid quality factor of "' . $qualityFactor . '" provided, must be between 0 and 1 (inclusive)'
            );
        }

        return new QualityFactor($qualityFactor);
    }
}
