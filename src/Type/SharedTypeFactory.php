<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 2/17/14
 * Time: 7:55 PM
 */

namespace ptlis\ConNeg\Type;


use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\QualityFactor\QualityFactor;

class SharedTypeFactory implements TypeFactoryInterface
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @var string
     */
    private $typeClass;


    /**
     * Constructor
     *
     * @param string $regex
     * @param string $typeClass
     */
    public function __construct($regex, $typeClass)
    {
        $this->regex = $regex;
        // Todo: ensure is instance of TypeInterface
        $this->typeClass = $typeClass;
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
    public function parse($field)
    {
        $typeCollection = new TypeCollection();

        if (preg_match_all($this->regex, $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList);

        } elseif (strlen($field)) {
            throw new ConNegException('Error parsing field');
        }

        return $typeCollection;
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
                $typeObj = new AbsentType();
                break;

            case '*':
                $typeObj = new WildcardType(new QualityFactor($qualityFactor));
                break;

            default:
                $typeObj = new $this->typeClass($type, new QualityFactor($qualityFactor));
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
}
