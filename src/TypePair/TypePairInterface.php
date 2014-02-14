<?php

/**
 * Interface for types.
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

namespace ptlis\ConNeg\TypePair;

use ptlis\ConNeg\Type\TypeInterface;

/**
 * Interface for types.
 */
interface TypePairInterface
{
    /**
     * Returns the user's preferred type or an instance of AbsentType.
     *
     * @return TypeInterface
     */
    public function getUserType();


    /**
     * Returns the application's preferred type or an instance of AbsentType.
     *
     * @return TypeInterface
     */
    public function getAppType();


    /**
     * Returns the product of the application & user-agent quality factors.
     *
     * @return float
     */
    public function getQualityFactorProduct();
}
