<?php

/**
 * Class for type pairs.
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

namespace ptlis\ConNeg\TypePair;

use ptlis\ConNeg\Type\Mime\MimeTypeInterface;

/**
 * Class for type pairs.
 */
class MimeTypePair implements TypePairInterface
{
    /**
     * @var SharedTypePair
     */
    private $sharedTypePair;


    /**
     * Constructor.
     *
     * @param MimeTypeInterface $userType
     * @param MimeTypeInterface $appType
     */
    public function __construct(MimeTypeInterface $appType, MimeTypeInterface $userType)
    {
        $this->sharedTypePair = new SharedTypePair($appType, $userType);
    }


    /**
     * Returns the user-agent's type or an instance of AbsentType.
     *
     * @return MimeTypeInterface
     */
    public function getUserType()
    {
        return $this->sharedTypePair->getUserType();
    }


    /**
     * Returns the application's type or an instance of AbsentType.
     *
     * @return MimeTypeInterface
     */
    public function getAppType()
    {
        return $this->sharedTypePair->getAppType();
    }


    /**
     * Get the shared type for this pair.
     *
     * @return string
     */
    public function getType()
    {
        return $this->sharedTypePair->getType();
    }


    /**
     * Returns the product of the application & user-agent quality factors.
     *
     * @return float
     */
    public function getQualityFactor()
    {
        return $this->sharedTypePair->getQualityFactor();
    }


    /**
     * Returns the combined precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return $this->getAppType()->getPrecedence() * $this->getUserType()->getPrecedence();
    }


    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->sharedTypePair = clone $this->sharedTypePair;
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
}
