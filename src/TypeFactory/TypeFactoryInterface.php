<?php

/**
 * Interface for factories that parse & create Types.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\TypeFactory;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Interface for factories that parse & create Types.
 */
interface TypeFactoryInterface
{
    /**
     * Parse application types as http field & return a collection of types.
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parseApp($field);


    /**
     * Parse user-agent types from http field & return a collection of types.
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parseUser($field);


    /**
     * @param string $type
     * @param string $qualityFactor
     * @param bool   $appType
     *
     * @return TypeInterface
     */
    public function get($type, $qualityFactor, $appType);
}
