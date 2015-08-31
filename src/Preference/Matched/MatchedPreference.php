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
class MatchedPreference implements MatchedPreferenceInterface
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
     * Get the shared variant for this pair.
     *
     * @return string
     */
    public function getVariant()
    {
        if (strlen($this->clientPref->getVariant()) && !strstr($this->clientPref->getVariant(), '*')) {
            return $this->clientPref->getVariant();
        } else {
            return $this->serverPref->getVariant();
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
        return $this->getServerPreference()->getPrecedence() + $this->getClientPreference()->getPrecedence();
    }

    /**
     * Create string representation of the preference.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getVariant() . ';q=' . $this->getQualityFactor();
    }
}
