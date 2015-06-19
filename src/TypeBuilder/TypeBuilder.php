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

namespace ptlis\ConNeg\TypeBuilder;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\Extens\AcceptExtens;
use ptlis\ConNeg\Type\Extens\AcceptExtensInterface;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\TypeInterface;

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
     * The name of the type.
     *
     * @var string
     */
    protected $type;

    /**
     * The quality factor associated with the type.
     *
     * @var float
     */
    protected $qFactor;

    /**
     * An array of accept-extens fragments for the type.
     *
     * @var AcceptExtensInterface[]
     */
    protected $acceptExtensList = array();


    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set accept-extens for the type.
     *
     * @throws InvalidTypeException
     *
     * @param array<array<string>> $acceptExtensList
     *
     * @return TypeBuilderInterface
     */
    public function setAcceptExtens(array $acceptExtensList)
    {
        foreach ($acceptExtensList as $rawAcceptExtens) {
            try {
                $this->acceptExtensList[] = $this->buildAcceptExtens($rawAcceptExtens);

            } catch (InvalidTypeException $e) {
                if ($this->appType) {
                    throw $e;
                }
            }
        }

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
                $type = new Type('', 0, Type::ABSENT_TYPE);
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
     * Build & return an type object representing an empty type.
     *
     * @return TypeInterface
     */
    public function getEmpty()
    {
        return $this
            ->setAppType(false)
            ->setType('')
            ->setQualityFactor('0')
            ->get();
    }

    /**
     * Build an accept-extens value object from it's array definition.
     *
     * @throws InvalidTypeException
     *
     * @param array<string> $rawAcceptExtens
     *
     * @return AcceptExtens
     */
    private function buildAcceptExtens(array $rawAcceptExtens)
    {
        // Simple value
        if (1 === count($rawAcceptExtens)) {
            $acceptExtens = new AcceptExtens($rawAcceptExtens[0]);

            // Key-value pair
        } elseif (3 === count($rawAcceptExtens)) {
            $acceptExtens = new AcceptExtens($rawAcceptExtens[2], $rawAcceptExtens[0]);

            // Invalid extens in application type
        } else {
            throw new InvalidTypeException(
                'Malformed accept-extens "' . implode($rawAcceptExtens) . '" found'
            );
        }

        return $acceptExtens;
    }

    /**
     * Attempt to get a wildcard type, throws exception if type was provided by the application.
     *
     * @throws InvalidTypeException
     *
     * @return Type
     */
    private function getWildcard()
    {
        if ($this->appType) {
            throw new InvalidTypeException(
                'Wildcards are not valid in application-provided types.'
            );
        }

        return new Type('*', $this->qFactor, Type::WILDCARD_TYPE);
    }

    /**
     * Set the default state of the builder.
     */
    protected function setDefaults()
    {
        $this->appType = false;
        $this->type = '';
        $this->qFactor = 1;
        $this->acceptExtensList = array();
    }

    /**
     * Returns a Type instances from the provided configuration.
     *
     * @return TypeInterface
     */
    protected function getType()
    {
        return new Type($this->type, $this->qFactor, Type::EXACT_TYPE);
    }
}
