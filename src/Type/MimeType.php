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
 * Class for MIME type.
 */
class MimeType implements MimeTypeInterface
{
    const ABSENT_TYPE = -1;
    const WILDCARD_TYPE = 0;
    const WILDCARD_SUBTYPE = 1;
    const EXACT_TYPE = 2;

    /**
     * The main type (eg text, application, image).
     *
     * @var string
     */
    private $mimeType;

    /**
     * The subtype (eg html, xml, json).
     *
     * @var string
     */
    private $subType;

    /**
     * The quality factor of this type.
     *
     * @var float
     */
    private $qFactor;

    /**
     * The precedence of this type (used for matching).
     *
     * @var int
     */
    private $precedence;


    /**
     * Constructor
     *
     * @param string $mimeType
     * @param string $subType
     * @param float $qFactor
     * @param int $precedence
     */
    public function __construct($mimeType, $subType, $qFactor, $precedence)
    {
        $this->mimeType = $mimeType;
        $this->subType = $subType;
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
        $str = '';
        if (strlen($this->subType)) {
            $str = $this->mimeType . '/' . $this->subType;
        }

        return $str;
    }

    /**
     * Returns the type portion of the media range.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Returns the subtype portion of the media range.
     *
     * @return string
     */
    public function getMimeSubType()
    {
        return $this->subType;
    }

    /**
     * Return the precedence of the type (wildcards are superseded by full matches etc).
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
