<?php

/**
 * Class for absence of a MIME type.
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

use ptlis\ConNeg\QualityFactor\QualityFactorInterface;
use ptlis\ConNeg\Type\Mime\Interfaces\MimeTypeInterface;

/**
 * Class for absence of a MIME type.
 */
class AbsentMimeType implements MimeTypeInterface
{
    /**
     * @var QualityFactorInterface
     */
    private $qFactor;


    /**
     * Constructor
     *
     * @param QualityFactorInterface $qFactor
     */
    public function __construct(QualityFactorInterface $qFactor)
    {
        $this->qFactor = $qFactor;
    }


    /**
     * Returns an empty string.
     *
     * @return string
     */
    public function getMimeType()
    {
        return '';
    }


    /**
     * Returns an empty string.
     *
     * @return string
     */
    public function getMimeSubType()
    {
        return '';
    }


    /**
     * Returns an empty string.
     *
     * @return string
     */
    public function getType()
    {
        return '';
    }


    /**
     * Return the precedence of the type, non-matching types have the lowest precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return -1;
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
     * Returns an empty string.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }


    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->qFactor = clone $this->qFactor;
    }
}
