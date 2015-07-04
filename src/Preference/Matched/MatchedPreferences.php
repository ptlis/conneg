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

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class for type pairs.
 */
class MatchedPreferences implements MatchedPreferencesInterface
{
    /**
     * The type from the User-Agent.
     *
     * @var PreferenceInterface
     */
    private $userType;

    /**
     * The type from the Application.
     *
     * @var PreferenceInterface
     */
    private $appType;


    /**
     * Constructor.
     *
     * @param PreferenceInterface $appType
     * @param PreferenceInterface $userType
     */
    public function __construct(PreferenceInterface $userType, PreferenceInterface $appType)
    {
        $this->userType = $userType;
        $this->appType  = $appType;
    }

    /**
     * Returns the user-agent's type or an instance of AbsentType.
     *
     * @return PreferenceInterface
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Returns the application's type or an instance of AbsentType.
     *
     * @return PreferenceInterface
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
     * Create string representation of type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getType() . ';q=' . $this->getQualityFactor();
    }
}
