<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class for matched preferences.
 */
class MatchedPreference implements MatchedPreferenceInterface
{
    /**
     * Which HTTP field the match relates to.
     *
     * @var string
     */
    private $fromField;

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
     * @param string $fromField
     * @param PreferenceInterface $serverPref
     * @param PreferenceInterface $clientPref
     */
    public function __construct($fromField, PreferenceInterface $clientPref, PreferenceInterface $serverPref)
    {
        $this->fromField = $fromField;
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
        // Special handling for language partial matches. These require that the returned variant name is the portion of
        // the language before the wildcard as this is what the application will be using to encode the response.
        if ($this->isLanguageWildcard()) {
            return str_replace('-*', '', $this->serverPref->getVariant());

        // If the client contained a wildcard or is absent return the concrete variant from teh server.
        } elseif ($this->clientWildcardOrAbsent()) {
            return $this->serverPref->getVariant();

        // In all other cases the client is canonical
        } else {
            return $this->clientPref->getVariant();
        }
    }

    /**
     * Returns true if the match was by partial language wildcard.
     *
     * @return bool
     */
    private function isLanguageWildcard()
    {
        return PreferenceInterface::LANGUAGE === $this->fromField
            && PreferenceInterface::PARTIAL_WILDCARD === $this->serverPref->getPrecedence();
    }

    /**
     * Returns true if the client contained a wildcard or was absent.
     *
     * @return bool
     */
    private function clientWildcardOrAbsent()
    {
        return !(strlen($this->clientPref->getVariant()) && !strstr($this->clientPref->getVariant(), '*'));
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
