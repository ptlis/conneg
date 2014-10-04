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

/**
 * Value class representing a quality factor.
 */
class QualityFactor implements QualityFactorInterface
{
    /**
     * The quality factor value range of 0-1.
     *
     * @var float
     */
    private $qFactor;


    /**
     * Constructor.
     *
     * @param float $qFactor
     */
    public function __construct($qFactor)
    {
        $this->qFactor = $qFactor;
    }

    /**
     * Returns the quality factor.
     *
     * @return float
     */
    public function getFactor()
    {
        return $this->qFactor;
    }

    /**
     * Returns a string representation of the quality factor.
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->getFactor());
    }
}
