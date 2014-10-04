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

namespace ptlis\ConNeg\Type;

use ptlis\ConNeg\QualityFactor\QualityFactorInterface;

/**
 * Class representing a type.
 */
class Type implements TypeInterface
{
    /**
     * The name of the type.
     *
     * @var string
     */
    private $type;

    /**
     * The quality factor associated with this type.
     *
     * @var QualityFactorInterface
     */
    private $qFactor;

    /**
     * Type precedence of the type (named > wildcard > absent).
     *
     * @var int
     */
    protected $precedence;


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
        $this->precedence = 1;
    }

    /**
     * Return the full type as a string.
     *
     * @return string
     */
    public function getType()
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
        return $this->precedence;
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
        return $this->getType() . ';q=' . $this->getQualityFactor();
    }
}
