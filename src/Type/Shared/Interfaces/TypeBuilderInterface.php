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

namespace ptlis\ConNeg\Type\Shared\Interfaces;

use ptlis\ConNeg\QualityFactor\QualityFactorFactory;

/**
 * Interface for type builders.
 */
interface TypeBuilderInterface
{
    /**
     * Constructor
     *
     * @param QualityFactorFactory $qFactorFactory
     */
    public function __construct(QualityFactorFactory $qFactorFactory);


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
