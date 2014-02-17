<?php

/**
 * Factory class to parse Accept-Encoding fields & create EncodingType instances.
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

namespace ptlis\ConNeg\Type\Encoding;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\TypeFactoryInterface;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Type\WildcardType;

/**
 * Factory class to parse Accept-Encoding fields & create EncodingType instances.
 */
class EncodingTypeFactory implements TypeFactoryInterface
{
    /**
     * Class providing regex to use for parsing the Accept-Charset field.
     *
     * @var EncodingRegexProviderInterface
     */
    private $regexProvider;


    /**
     * Constructor
     *
     * @param EncodingRegexProviderInterface $regex
     */
    public function __construct(EncodingRegexProviderInterface $regex)
    {
        $this->regexProvider = $regex;
    }


    /**
     * Parse the provided Accept-Encoding field & return a TypeCollection containing EncodingType & WildcardType i
     * instances.
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parse($field)
    {
        $typeCollection = new TypeCollection();

        if (preg_match_all($this->regexProvider->getEncodingRegex(), $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList);

        } else {
            // TODO: Throw exception
        }

        return $typeCollection;
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

            if ('*' === $typeList['type'][$key]) {
                $type = new WildcardType(new QualityFactor($qFactor));
            } else {
                $type = new EncodingType($typeList['type'][$key], new QualityFactor($qFactor));
            }

            $typeCollection->addType($type);
        }
    }


    /**
     * Build & return EncodingType from params.
     *
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
                $typeObj = new EncodingType($type, new QualityFactor($qualityFactor));
                break;
        }

        return $typeObj;
    }
}
