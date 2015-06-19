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

namespace ptlis\ConNeg\TypePair;

use ptlis\ConNeg\Type\TypeInterface;

/**
 * Class for type pairs.
 */
class TypePair implements TypePairInterface
{
    /**
     * The type from the User-Agent.
     *
     * @var TypeInterface
     */
    private $userType;

    /**
     * The type from the Application.
     *
     * @var TypeInterface
     */
    private $appType;


    /**
     * Constructor.
     *
     * @param TypeInterface $appType
     * @param TypeInterface $userType
     */
    public function __construct(TypeInterface $userType, TypeInterface $appType)
    {
        $this->userType = $userType;
        $this->appType  = $appType;
    }

    /**
     * Returns the user-agent's type or an instance of AbsentType.
     *
     * @return TypeInterface
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Returns the application's type or an instance of AbsentType.
     *
     * @return TypeInterface
     */
    public function getAppType()
    {
        return $this->appType;
    }

    /**
     * Get the shared type for this pair.
     *
     * @return string
     */
    public function getType()
    {
        if (strlen($this->userType->getType()) && !strstr($this->userType->getType(), '*')) {
            return $this->userType->getType();
        } else {
            return $this->appType->getType();
        }
    }

    /**
     * Returns the product of the application & user-agent quality factors.
     *
     * @return float
     */
    public function getQualityFactor()
    {
        return $this->userType->getQualityFactor() * $this->appType->getQualityFactor();
    }

    /**
     * Returns the combined precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return $this->getAppType()->getPrecedence() + $this->getUserType()->getPrecedence();
    }

    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->userType = clone $this->userType;
        $this->appType = clone $this->appType;
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
