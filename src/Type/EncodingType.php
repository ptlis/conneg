<?php

/**
 * Class for representing an Encoding type.
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

namespace ptlis\ConNeg\Type;

use ptlis\ConNeg\QualityFactor\QualityFactorInterface;

/**
 * Class for representing an Encoding type.
 */
class EncodingType implements TypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var QualityFactorInterface
     */
    private $qFactor;


    /**
     * Constructor
     *
     * @param string $type
     * @param QualityFactorInterface $qFactor
     */
    public function __construct($type, QualityFactorInterface $qFactor)
    {
        $this->type = $type;
        $this->qFactor = $qFactor;
    }


    /**
     * Return the full type as a string.
     *
     * @return string
     */
    public function getFullType()
    {
        return $this->type;
    }


    /**
     * Return the precedence of the type, non-wildcard type have the highest precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return 0;
    }


    /**
     * Returns the quality factor for the type.
     *
     * @return QualityFactorInterface
     */
    public function getQualityFactor()
    {
        return $this->qFactor;
    }


    /**
     * Create string representation of type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullType() . ';' . $this->getQualityFactor();
    }


    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->qFactor = clone $this->qFactor;
    }
}
