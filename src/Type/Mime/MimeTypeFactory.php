<?php

/**
 * Factory class to parse Accept fields & create MimeType instances.
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

namespace ptlis\ConNeg\Type\Mime;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\TypeFactoryInterface;

/**
 * Factory class to parse Accept fields & create MimeType instances.
 */
class MimeTypeFactory implements TypeFactoryInterface
{
    /**
     * Class providing regex to use for parsing the Accept-Charset field.
     *
     * @var MimeRegexProviderInterface
     */
    private $regexProvider;

    /**
     * @var QualityFactorFactory
     */
    private $qualityFactorFactory;


    /**
     * Constructor
     *
     * @param MimeRegexProviderInterface $regex
     * @param QualityFactorFactory       $qualityFactorFactory
     */
    public function __construct(MimeRegexProviderInterface $regex, QualityFactorFactory $qualityFactorFactory)
    {
        $this->regexProvider = $regex;
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
     * Extracts type data from $typeList and populates typeCollection with CharsetTypes.
     *
     * @param TypeCollection $typeCollection
     * @param array          $typeList
     */
    private function getFromArray(TypeCollection $typeCollection, array $typeList)
    {
        foreach (array_keys($typeList['type']) as $key) {

            preg_match_all(
                $this->regexProvider->getAcceptExtensRegex(),
                $typeList['extens'][$key],
                $extensList
            );

            // TODO: Not really the correct behaviour; that depends upon application or user-agent types
            $normalisedExtensList = array();
            foreach (array_keys($extensList['key']) as $extensKey) {
                $normalisedExtensList[$extensList['key'][$extensKey]] = $extensList['value'][$extensKey];
            }

            if (array_key_exists('q', $normalisedExtensList)) {
                $qFactor = $normalisedExtensList['q'];
                unset($normalisedExtensList['q']);
            } else {
                $qFactor = 1;
            }

            $type = $this->get($typeList['type'][$key], $qFactor);

            $typeCollection->addType($type);
        }
    }


    /**
     * Build & return LanguageType from params.
     *
     * @throws InvalidTypeException
     *
     * @param string $type
     * @param string $qualityFactor
     *
     * @return MimeTypeInterface
     */
    public function get($type, $qualityFactor)
    {
        $explodedType = explode('/', $type);
        if (2 == count($explodedType)) {
            list($mimeType, $subType) = $explodedType;
            $typeObj = $this->getFromParts($mimeType, $subType, $qualityFactor);

        } elseif (!strlen($type)) {
            $typeObj = new AbsentMimeType($this->qualityFactorFactory->get(0));

        } else {
            throw new InvalidTypeException(
                '"' . $type . '" is not a valid mime type'
            );
        }

        return $typeObj;
    }


    /**
     * Get the type from parts.
     *
     * @throws InvalidTypeException
     *
     * @param string $mimeType
     * @param string $subType
     * @param string $qualityFactor
     *
     * @return MimeTypeInterface
     */
    private function getFromParts($mimeType, $subType, $qualityFactor)
    {
        switch (true) {
            // Full wildcard type
            case $mimeType === '*' && $subType === '*':
                $typeObj = new MimeWildcardType($this->qualityFactorFactory->get($qualityFactor));
                break;

            // Wildcard subtype
            case $mimeType !== '*' && $subType === '*':
                $typeObj = new MimeWildcardSubType($mimeType, $this->qualityFactorFactory->get($qualityFactor));
                break;

            // Wildcard type
            case $mimeType === '*' && $subType !== '*':
                throw new InvalidTypeException(
                    '"' . $mimeType . '/' . $subType . '" is not a valid mime type'
                );
                break;

            default:
                $typeObj = new MimeType($mimeType, $subType, $this->qualityFactorFactory->get($qualityFactor));
                break;
        }

        return $typeObj;
    }


    /**
     * Parse the provided Accept field & return a TypeCollection containing MimeType, MimeWildcardType &
     * MimeWildcardSubType Instances.
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

        if (preg_match_all($this->regexProvider->getMimeRegex(), $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList);

        } elseif (strlen($field)) {
            throw new ConNegException('Error parsing field');
        }

        return $typeCollection;
    }
}
