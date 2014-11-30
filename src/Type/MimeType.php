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
use ptlis\ConNeg\Type\Extens\AcceptExtensInterface;

/**
 * Class for MIME type.
 */
class MimeType implements MimeTypeInterface
{
    /**
     * The main type (eg text, application, image).
     *
     * @var string
     */
    private $type;

    /**
     * The subtype (eg html, xml, json).
     *
     * @var string
     */
    private $subType;

    /**
     * The quality factor associated with this type.
     *
     * @var QualityFactorInterface
     */
    private $qFactor;

    /**
     * An array of accept-extens fragments.
     *
     * @var AcceptExtensInterface[]
     */
    private $acceptExtensList;

    /**
     * Type precedence of the type (named > subtype wildcard > wildcard > absent).
     *
     * @var int
     */
    protected $precedence;


    /**
     * Constructor
     *
     * @param string $type
     * @param string $subType
     * @param QualityFactorInterface $qFactor
     * @param AcceptExtensInterface[] $acceptExtensList
     */
    public function __construct($type, $subType, QualityFactorInterface $qFactor, array $acceptExtensList = array())
    {
        $this->type = $type;
        $this->subType = $subType;
        $this->qFactor = $qFactor;
        $this->acceptExtensList = $acceptExtensList;
        $this->precedence = 2;
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
     * Return an array of accept-extens fragments.
     *
     * @return AcceptExtensInterface[]
     */
    public function getExtens()
    {
        return $this->acceptExtensList;
    }

    /**
     * Create string representation of type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getType() . ';q=' . $this->getQualityFactor() . implode(';', $this->acceptExtensList);
    }
}
