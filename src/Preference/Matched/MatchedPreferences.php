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
     * The preference from the client.
     *
     * @var PreferenceInterface
     */
    private $clientPref;

    /**
     * The preference from the server.
     *
     * @var PreferenceInterface
     */
    private $serverPref;


    /**
     * Constructor.
     *
     * @param PreferenceInterface $serverPref
     * @param PreferenceInterface $clientPref
     */
    public function __construct(PreferenceInterface $clientPref, PreferenceInterface $serverPref)
    {
        $this->clientPref = $clientPref;
        $this->serverPref  = $serverPref;
    }

    /**
     * @inheritDoc
     */
    public function getClientPreference()
    {
        return $this->clientPref;
    }

    /**
     * @inheritDoc
     */
    public function getServerPreference()
    {
        return $this->serverPref;
    }

    /**
     * Get the shared type for this pair.
     *
     * @return string
     */
    public function getType()
    {
        if (strlen($this->clientPref->getType()) && !strstr($this->clientPref->getType(), '*')) {
            return $this->clientPref->getType();
        } else {
            return $this->serverPref->getType();
        }
    }

    /**
     * Returns the product of the server & client quality factors.
     *
     * @return float
     */
    public function getQualityFactor()
    {
        return $this->clientPref->getQualityFactor() * $this->serverPref->getQualityFactor();
    }

    /**
     * Returns the combined precedence.
     *
     * @return int
     */
    public function getPrecedence()
    {
        // TODO: Wrong behaviour? Perhaps return the lowest precedence...
        return $this->getServerPreference()->getPrecedence() + $this->getClientPreference()->getPrecedence();
    }

    /**
     * @inheritDoc
     */
    public function getFromField()
    {
        return $this->getServerPreference()->getFromField();
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
