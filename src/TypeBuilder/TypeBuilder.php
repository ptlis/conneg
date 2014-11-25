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

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Exception\QualityFactorException;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\QualityFactor\QualityFactorInterface;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Type\WildcardType;

/**
 * Type builder for shared functionality.
 */
class TypeBuilder implements TypeBuilderInterface
{
    /**
     * Whether the type was provided by teh application or user-agent.
     *
     * @var bool
     */
    protected $appType;

    /**
     * Factory for creating quality factor value objects from floats.
     *
     * @var QualityFactorFactory
     */
    protected $qFactorFactory;

    /**
     * The name of the type.
     *
     * @var string
     */
    protected $type;

    /**
     * The quality factor associated with the type.
     *
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
     * @throws InvalidTypeException
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
                $type = $this->getWildcard();
                break;


            default:
                $type = $this->getType();
                break;
        }

        $this->setDefaults();

        return $type;
    }

    /**
     * Attempt to get a wildcard type, throws exception if type was provided by the application.
     *
     * @throws InvalidTypeException
     *
     * @return WildcardType
     */
    private function getWildcard()
    {
        if ($this->appType) {
            throw new InvalidTypeException(
                'Wildcards are not valid in application-provided types.'
            );
        }

        return new WildcardType($this->qFactor);
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
     * @return TypeInterface
     */
    protected function getType()
    {
        return new Type($this->type, $this->qFactor);
    }
}