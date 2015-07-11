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

namespace ptlis\ConNeg\Preference\Builder;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface for type builders.
 */
interface PreferenceBuilderInterface
{
    /**
     * Set whether the build type is application-defined or user-defined.
     *
     * @param bool $isFromApp
     *
     * @return $this
     */
    public function setFromApp($isFromApp);

    /**
     * Sets the HTTP field that the preference was derived from.
     *
     * @param string $fromField
     *
     * @return $this
     */
    public function setFromField($fromField);

    /**
     * Set the string representation of the type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Set the quality factor.
     *
     * @param float $qFactor
     *
     * @return $this
     */
    public function setQualityFactor($qFactor);

    /**
     * Get the hydrated type object.
     *
     * @return PreferenceInterface
     */
    public function get();
}
