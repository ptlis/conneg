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
 * Class for matched preferences.
 */
class MatchedPreferences implements MatchedPreferencesInterface
{
    /**
     * The preference from the User-Agent.
     *
     * @var PreferenceInterface
     */
    private $userPreference;

    /**
     * The preference from the Application.
     *
     * @var PreferenceInterface
     */
    private $appPreference;


    /**
     * Constructor.
     *
     * @param PreferenceInterface $appPreference
     * @param PreferenceInterface $userPreference
     */
    public function __construct(PreferenceInterface $userPreference, PreferenceInterface $appPreference)
    {
        $this->userPreference = $userPreference;
        $this->appPreference  = $appPreference;
    }

    /**
     * @inheritDoc
     */
    public function getUserPreference()
    {
        return $this->userPreference;
    }

    /**
     * @inheritDoc
     */
    public function getAppPreference()
    {
        return $this->appPreference;
    }

    /**
     * Get the shared type for this pair.
     *
     * @return string
     */
    public function getType()
    {
        if (strlen($this->userPreference->getType()) && !strstr($this->userPreference->getType(), '*')) {
            return $this->userPreference->getType();
        } else {
            return $this->appPreference->getType();
        }
    }

    /**
     * Returns the product of the application & user-agent quality factors.
     *
     * @return float
     */
    public function getQualityFactor()
    {
        return $this->userPreference->getQualityFactor() * $this->appPreference->getQualityFactor();
    }

    /**
     * Returns the combined precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        return $this->getAppPreference()->getPrecedence() + $this->getUserPreference()->getPrecedence();
    }

    /**
     * @inheritDoc
     */
    public function getFromField()
    {
        return $this->getAppPreference()->getFromField();
    }

    /**
     * Create string representation of the preference.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getType() . ';q=' . $this->getQualityFactor();
    }
}
