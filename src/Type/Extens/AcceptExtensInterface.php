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

namespace ptlis\ConNeg\Type\Extens;

/**
 * Interface for a fragment from the accept-extens parameters.
 */
interface AcceptExtensInterface
{
    /**
     * Returns true if type has a key & value, false if only a value.
     *
     * @return bool
     */
    public function isCompound();

    /**
     * Returns the key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Returns the value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Returns the string representation of the accept-extens.
     *
     * @return string
     */
    public function __toString();
}
