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

namespace ptlis\ConNeg\Type\Shared;

use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\QualityFactor\QualityFactorInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeBuilderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface;

/**
 * Abstract type builder for shared functionality.
 */
abstract class AbstractTypeBuilder implements TypeBuilderInterface
{
    /**
     * @var bool
     */
    protected $appType;

    /**
     * @var QualityFactorFactory
     */
    protected $qFactorFactory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var float
     */
    protected $qFactor;


    /**
     * Constructor
     *
     * @param QualityFactorFactory $qFactorFactory
     */
    public function __construct(QualityFactorFactory $qFactorFactory)
    {
        $this->qFactorFactory = $qFactorFactory;
        $this->setDefaults();
    }


    /**
     * Set whether the build type is application-defined or user-defined.
     *
     * @param bool $appType
     *
     * @return TypeBuilderInterface
     */
    public function setAppType($appType)
    {
        $this->appType = $appType;

        return $this;
    }


    /**
     * Set the string representation of the type.
     *
     * @param string $type
     *
     * @return TypeBuilderInterface
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the quality factor.
     *
     * @param float $qFactor
     *
     * @return TypeBuilderInterface
     */
    public function setQualityFactor($qFactor)
    {
        $this->qFactor = $qFactor;

        return $this;
    }


    /**
     * Validate the builder state, if valid then return the hydrated type object.
     *
     * @throws ConNegException
     *
     * @return TypeInterface
     */
    public function get()
    {
        if (gettype($this->type) !== 'string') {
            throw new ConNegException('Invalid type provided to builder.');
        }

        switch ($this->type) {
            case '':
                $type = new AbsentType($this->qFactorFactory->get(0, $this->appType));
                break;

            case '*':
                $type = new WildcardType($this->qFactorFactory->get($this->qFactor, $this->appType));
                break;

            default:
                $type = $this->getType();
                break;
        }

        $this->setDefaults();

        return $type;
    }


    /**
     * Set the default state of the builder.
     */
    private function setDefaults()
    {
        $this->appType = false;
        $this->type = '';
        $this->qFactor = 0;
    }


    /**
     * Get the type object from the provided specification.
     *
     * @return TypeInterface
     */
    abstract protected function getType();
}
