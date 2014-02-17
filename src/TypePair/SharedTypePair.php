<?php

/**
 * Class for type pairs, used for encoding, charset & language negotiation.
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

use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Class for type pairs.
 */
class SharedTypePair implements TypePairInterface
{
    /**
     * @var TypeInterface
     */
    private $userType;

    /**
     * @var TypeInterface
     */
    private $appType;


    /**
     * Constructor.
     *
     * @param TypeInterface $userType
     * @param TypeInterface $appType
     */
    public function __construct(TypeInterface $appType, TypeInterface $userType)
    {
        $this->userType = $userType;
        $this->appType = $appType;
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
        if (strlen($this->userType->getType())) {
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
    public function getQualityFactorProduct()
    {
        return $this->userType->getQualityFactor()->getFactor() * $this->appType->getQualityFactor()->getFactor();
    }


    /**
     * Deep clone.
     */
    public function __clone()
    {
        $this->userType = clone $this->userType;
        $this->appType = clone $this->appType;
    }
}
