<?php

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
    public function setFromServer($isFromServer);

    /**
     * Sets the HTTP field that the preference was derived from.
     *
     * @param string $fromField
     *
     * @return $this
     */
    public function setFromField($fromField);

    /**
     * Set the string representation of the variant.
     *
     * @param string $variant
     *
     * @return $this
     */
    public function setVariant($variant);

    /**
     * Set the quality factor.
     *
     * @param float $qFactor
     *
     * @return $this
     */
    public function setQualityFactor($qFactor);

    /**
     * Get the hydrated preference object.
     *
     * @return PreferenceInterface
     */
    public function get();
}
