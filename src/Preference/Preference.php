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
    /**
     * The field that this type preference was derived from.
     *
     * @var string
     */
    private $fromField;

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
     * @param string $field
     * @param string $type
     * @param float $qFactor
     * @param int $precedence
     */
    public function __construct($field, $type, $qFactor, $precedence)
    {
        $this->fromField = $field;
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
     * @inheritDoc
     */
    public function getFromField()
    {
        return $this->fromField;
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
