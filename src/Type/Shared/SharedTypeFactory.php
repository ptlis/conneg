<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 2/17/14
 * Time: 7:55 PM
 */

namespace ptlis\ConNeg\Type\Shared;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeRegexProviderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeFactoryInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface;

class SharedTypeFactory implements TypeFactoryInterface
{
    /**
     * @var TypeRegexProviderInterface
     */
    private $regexProvider;

    /**
     * @var string
     */
    private $typeClass;

    /**
     * @var QualityFactorFactory
     */
    private $qualityFactorFactory;


    /**
     * Constructor
     *
     * @throws ConNegException
     *
     * @param TypeRegexProviderInterface $regexProvider
     * @param string $typeClass
     * @param QualityFactorFactory $qualityFactorFactory
     */
    public function __construct(
        TypeRegexProviderInterface $regexProvider,
        $typeClass,
        QualityFactorFactory $qualityFactorFactory
    ) {
        if (!is_subclass_of($typeClass, 'ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface')) {
            throw new ConNegException(
                '"' . $typeClass . '" does not implement TypeInterface'
            );
        }

        $this->regexProvider = $regexProvider;
        $this->typeClass = $typeClass;
        $this->qualityFactorFactory = $qualityFactorFactory;
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
     * @param string $type
     * @param string $qualityFactor
     *
     * @return TypeInterface
     */
    public function get($type, $qualityFactor)
    {
        switch ($type) {
            case '':
                $typeObj = new AbsentType($this->qualityFactorFactory->get(0));
                break;

            case '*':
                $typeObj = new WildcardType($this->qualityFactorFactory->get($qualityFactor));
                break;

            default:
                $typeObj = new $this->typeClass($type, $this->qualityFactorFactory->get($qualityFactor));
                break;
        }

        return $typeObj;
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
