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

namespace ptlis\ConNeg\TypeFactory;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\RegexProvider\TypeRegexProviderInterface;
use ptlis\ConNeg\TypeBuilder\TypeBuilderInterface;
use ptlis\ConNeg\Type\TypeInterface;

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
        return $this->parse($field, true);
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
        return $this->parse($field, false);
    }


    /**
     * Get a type from the provided parameters.
     *
     * @param string $type
     * @param string $qualityFactor
     * @param bool $appType
     *
     * @return TypeInterface
     */
    public function get($type, $qualityFactor, $appType = false)
    {
        return $this->typeBuilder
            ->setAppType($appType)
            ->setType($type)
            ->setQualityFactor($qualityFactor)
            ->get();
    }


    /**
     * Extracts type data from $typeList and populates typeCollection with CharsetType & WildcardType instances..
     *
     * @param TypeCollection|TypeInterface[] $typeCollection
     * @param array          $typeList
     * @param bool           $appType
     */
    private function getFromArray(TypeCollection $typeCollection, array $typeList, $appType)
    {
        foreach (array_keys($typeList['qfactor']) as $key) {
            $type = $this->get($typeList['type'][$key], $typeList['qfactor'][$key], $appType);

            $typeCollection->addType($type);
        }
    }


    /**
     * Parse a http field & return a collection of types.
     *
     * @throws ConNegException
     *
     * @param string $field
     * @param bool $appType
     *
     * @return TypeCollection
     */
    private function parse($field, $appType)
    {
        $typeCollection = new TypeCollection();

        if (preg_match_all($this->regexProvider->getTypeRegex(), $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList, $appType);

        } elseif (strlen($field) && $appType) {
            throw new ConNegException('Error parsing field');
        }

        return $typeCollection;
    }
}
