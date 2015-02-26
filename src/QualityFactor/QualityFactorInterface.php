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

namespace ptlis\ConNeg\QualityFactor;

/**
 * Interface for quality factors.
 */
interface QualityFactorInterface
{
    /**
     * Returns the quality factor.
     *
     * @return float
     */
    public function getFactor();

    /**
     * Returns a string representation of the quality factor.
     *
     * @return string
     */
    public function __toString();
}
