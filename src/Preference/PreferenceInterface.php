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

namespace ptlis\ConNeg\Preference;

/**
 * Interface for value types storing type preferences.
 */
interface PreferenceInterface
{
    /**
     * Return the full type as a string.
     *
     * @return string
     */
    public function getType();

    /**
     * Return the precedence of the type (wildcards are superseded by full matches etc).
     *
     * @return int
     */
    public function getPrecedence();

    /**
     * Returns the quality factor for the type.
     *
     * @return float
     */
    public function getQualityFactor();

    /**
     * Create string representation of type.
     *
     * @return string
     */
    public function __toString();
}