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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeBuilderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeRegexProviderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeFactoryInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface;

/**
 * Factory for creation of Charset, Encoding and Language types.
 */
class SharedTypeFactory implements TypeFactoryInterface
{
    /**
     * @var TypeRegexProviderInterface
     */
    private $regexProvider;

    /**
     * @var TypeBuilderInterface
     */
    private $typeBuilder;


    /**
     * Constructor
     *
     * @throws ConNegException
     *
     * @param TypeRegexProviderInterface $regexProvider
     * @param TypeBuilderInterface $typeBuilder
     */
    public function __construct(
        TypeRegexProviderInterface $regexProvider,
        TypeBuilderInterface $typeBuilder
    ) {
        $this->regexProvider = $regexProvider;
        $this->typeBuilder = $typeBuilder;
    }


    /**
     * Parse application types as http field & return a collection of types.
     *
     * @throws ConNegException
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parseApp($field)
    {
        return $this->parse($field);
    }


    /**
     * Parse user-agent types from http field & return a collection of types.
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parseUser($field)
    {
        try {
            $userTypes = $this->parse($field);

        } catch (ConNegException $e) {
            $userTypes = new TypeCollection();
        }

        return $userTypes;
    }


    /**
     * Get a type from the provided parameters.
     *
     * @param string $type
     * @param string $qualityFactor
     *
     * @return TypeInterface
     */
    public function get($type, $qualityFactor)
    {
        return $this->typeBuilder
            ->setType($type)
            ->setQualityFactor($qualityFactor)
            ->get();
    }


    /**
     * Extracts type data from $typeList and populates typeCollection with CharsetType & WildcardType instances..
     *
     * @param TypeCollection $typeCollection
     * @param array          $typeList
     */
    private function getFromArray(TypeCollection $typeCollection, array $typeList)
    {
        foreach (array_keys($typeList['qfactor']) as $key) {

            $qFactor = filter_var($typeList['qfactor'][$key], FILTER_VALIDATE_FLOAT);

            // TODO: Not really the correct behaviour; that depends upon application or user-agent types
            if (false === $qFactor) {
                $qFactor = 1;
            }

            $type = $this->get($typeList['type'][$key], $qFactor);

            $typeCollection->addType($type);
        }
    }


    /**
     * Parse a http field & return a collection of types.
     *
     * @throws ConNegException
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    private function parse($field)
    {
        $typeCollection = new TypeCollection();

        if (preg_match_all($this->regexProvider->getTypeRegex(), $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList);

        } elseif (strlen($field)) {
            throw new ConNegException('Error parsing field');
        }

        return $typeCollection;
    }
}
