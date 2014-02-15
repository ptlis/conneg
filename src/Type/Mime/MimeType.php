<?php

/**
 * Class for MIME type.
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

namespace ptlis\ConNeg\Type\Mime;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorInterface;

/**
 * Class for MIME type.
 */
class MimeType implements MimeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subType;

    /**
     * @var QualityFactorInterface
     */
    private $qFactor;


    /**
     * Constructor
     *
     * @param string $type
     * @param string $subType
     * @param QualityFactorInterface|null $qFactor
     */
    public function __construct($type, $subType, QualityFactorInterface $qFactor = null)
    {
        if (is_null($qFactor)) {
            $qFactor = new QualityFactor(1);
        }

        $this->type = $type;
        $this->subType = $subType;
        $this->qFactor = $qFactor;
    }


    /**
     * Returns the type portion of the media range.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->type;
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
     * Return the full type as a string.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getMimeType() . '/' . $this->getMimeSubType();
    }


    /**
     * Return the precedence of the type, non-wildcard type have the highest precedence when you ignore accept-extens.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return 2;
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
