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

use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Exception\QualityFactorException;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\QualityFactor\QualityFactorInterface;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Type\WildcardType;

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
     * @var QualityFactorInterface
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
     * @throws InvalidTypeException
     *
     * @param string $type
     *
     * @return TypeBuilderInterface
     */
    public function setType($type)
    {
        if (gettype($type) !== 'string') {
            throw new InvalidTypeException('Invalid type provided to builder.');
        }

        $this->type = $type;

        return $this;
    }


    /**
     * Set the quality factor.
     *
     * @throws QualityFactorException
     *
     * @param float $qFactor
     *
     * @return TypeBuilderInterface
     */
    public function setQualityFactor($qFactor)
    {
        $this->qFactor = $this->qFactorFactory->get($qFactor, $this->appType);

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
        switch ($this->type) {
            case '':
                $type = new AbsentType($this->qFactor);
                break;

            case '*':
                if ($this->appType) {
                    throw new InvalidTypeException(
                        'Wildcards are not valid in application-provided types.'
                    );
                }
                $type = new WildcardType($this->qFactor);
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
        $this->qFactor = $this->qFactorFactory->get(0, $this->appType);
    }


    /**
     * Get the type object from the provided specification.
     *
     * @return TypeInterface
     */
    abstract protected function getType();
}
