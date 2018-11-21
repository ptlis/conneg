<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference;

/**
 * Value type storing variant preferences.
 */
class Preference implements PreferenceInterface
{
    /**
     * The variant name.
     *
     * @var string
     */
    private $variant;

    /**
     * The quality factor associated with this variant.
     *
     * @var float
     */
    private $qFactor;

    /**
     * Precedence of the variant (full > partial wildcard > wildcard > absent).
     *
     * @var int
     */
    protected $precedence;


    /**
     * Constructor
     *
     * @param string $variant
     * @param float $qFactor
     * @param int $precedence
     */
    public function __construct($variant, $qFactor, $precedence)
    {
        $this->variant = $variant;
        $this->qFactor = $qFactor;
        $this->precedence = $precedence;
    }

    /**
     * @inheritDoc
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * @inheritDoc
     */
    public function getPrecedence()
    {
        return $this->precedence;
    }

    /**
     * @inheritDoc
     */
    public function getQualityFactor()
    {
        return $this->qFactor;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        $str = '';
        if (strlen($this->getVariant())) {
            $str = $this->getVariant() . ';q=' . $this->getQualityFactor();
        }
        return $str;
    }
}
