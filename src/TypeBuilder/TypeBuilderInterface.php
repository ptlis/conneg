<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\TypeBuilder;

use ptlis\ConNeg\Type\TypeInterface;

/**
 * Interface for type builders.
 */
interface TypeBuilderInterface
{
    /**
     * Set whether the build type is application-defined or user-defined.
     *
     * @param bool $appType
     *
     * @return TypeBuilderInterface
     */
    public function setAppType($appType);

    /**
     * Set the string representation of the type.
     *
     * @param string $type
     *
     * @return TypeBuilderInterface
     */
    public function setType($type);

    /**
     * Set the quality factor.
     *
     * @param float $qFactor
     *
     * @return TypeBuilderInterface
     */
    public function setQualityFactor($qFactor);

    /**
     * Get the hydrated type object.
     *
     * @return TypeInterface
     */
    public function get();
}
