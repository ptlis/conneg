<?php

/**
 * Class for representing a Language type.
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

namespace ptlis\ConNeg\Type\Language;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorInterface;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Class for representing a Language type.
 */
class LanguageType implements TypeInterface
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
        return 1;
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


    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->qFactor = clone $this->qFactor;
    }
}
