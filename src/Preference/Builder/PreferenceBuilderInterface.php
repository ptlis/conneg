<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Builder;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface for preference builders.
 */
interface PreferenceBuilderInterface
{
    /**
     * Set whether the preference originates from the server.
     *
     * @param bool $isFromServer
     *
     * @return $this
     */
    public function setFromServer(bool $isFromServer): self;

    /**
     * Sets the HTTP field that the preference was derived from.
     *
     * @param string $fromField
     *
     * @return $this
     */
    public function setFromField(string $fromField): self;

    /**
     * Set the string representation of the variant.
     *
     * @param string $variant
     *
     * @return $this
     */
    public function setVariant(string $variant): self;

    /**
     * Set the quality factor.
     *
     * @param float $qFactor
     *
     * @return $this
     */
    public function setQualityFactor(float $qFactor): self;

    /**
     * Get the hydrated preference object.
     *
     * @return PreferenceInterface
     */
    public function get(): PreferenceInterface;
}
