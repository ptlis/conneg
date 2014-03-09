<?php

/**
 * Class for MIME with wildcard type & subtype.
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
 * Class for MIME with wildcard type & subtype.
 */
class MimeWildcardType implements MimeTypeInterface
{
    /**
     * @var QualityFactorInterface
     */
    private $qFactor;


    /**
     * Constructor.
     *
     * @param QualityFactorInterface $qFactor
     */
    public function __construct(QualityFactorInterface $qFactor)
    {
        $this->qFactor = $qFactor;
    }


    /**
     * Returns the type portion of the media range.
     *
     * @return string
     */
    public function getMimeType()
    {
        return '*';
    }


    /**
     * Returns the subtype portion of the media range.
     *
     * @return string
     */
    public function getMimeSubType()
    {
        return '*';
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
     * Return the precedence of the type, wildcard matches have the lowest precedence of matching types.
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
