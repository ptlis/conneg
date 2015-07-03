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

namespace ptlis\ConNeg\Type;

/**
 * Class representing a type.
 */
class Type implements TypeInterface
{
    const ABSENT_TYPE = -1;
    const WILDCARD_TYPE = 0;
    const WILDCARD_PARTIAL_LANG = 1;
    const WILDCARD_SUBTYPE = 1;
    const EXACT_TYPE = 2;

    /**
     * The name of the type.
     *
     * @var string
     */
    private $type;

    /**
     * The quality factor associated with this type.
     *
     * @var float
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
     * @param float $qFactor
     * @param int $precedence
     */
    public function __construct($type, $qFactor, $precedence)
    {
        $this->type = $type;
        $this->qFactor = $qFactor;
        $this->precedence = $precedence;
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
     * @return float
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
        $str = '';
        if (strlen($this->getType())) {
            $str = $this->getType() . ';q=' . $this->getQualityFactor();
        }
        return $str;
    }
}
