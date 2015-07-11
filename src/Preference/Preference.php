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

namespace ptlis\ConNeg\Preference;

/**
 * Value types storing type preferences.
 */
class Preference implements PreferenceInterface
{
    /** Null/absent type, used as a placeholder for matched preferences. */
    const ABSENT_TYPE = -1;

    /** Wildcard match */
    const WILDCARD = 0;

    /** Partial wildcard (e.g. text/* or en-*) */
    const PARTIAL_WILDCARD = 1;

    /** Fully qualified type */
    const COMPLETE = 2;

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
