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
use ptlis\ConNeg\Type\Mime\Interfaces\MimeTypeInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeRegexProviderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeFactoryInterface;

/**
 * Factory class to parse Accept fields & create MimeType instances.
 */
class MimeTypeFactory implements TypeFactoryInterface
{
    /**
     * Class providing regex to use for parsing the Accept-Charset field.
     *
     * @var TypeRegexProviderInterface
     */
    private $regexProvider;

    /**
     * @var MimeTypeBuilder
     */
    private $typeBuilder;


    /**
     * Constructor
     *
     * @param MimeTypeRegexProvider $regex
     * @param MimeTypeBuilder       $typeBuilder
     */
    public function __construct(MimeTypeRegexProvider $regex, MimeTypeBuilder $typeBuilder)
    {
        $this->regexProvider = $regex;
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
     * Extracts type data from $typeList and populates typeCollection with CharsetTypes.
     *
     * @throws InvalidTypeException
     *
     * @param TypeCollection $typeCollection
     * @param array          $typeList
     * @param bool           $appType
     */
    private function getFromArray(TypeCollection $typeCollection, array $typeList, $appType)
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

            try {
                $type = $this->get($typeList['type'][$key], $qFactor, $appType);

                $typeCollection->addType($type);

            } catch (InvalidTypeException $e) {
                // Suppress the error for user-agent fields
                if ($appType) {
                    throw $e;
                }
            }
        }
    }


    /**
     * Build & return LanguageType from params.
     *
     * @throws InvalidTypeException
     *
     * @param string $type
     * @param string $qualityFactor
     * @param bool $appType
     *
     * @return MimeTypeInterface
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
     * Parse the provided Accept field & return a TypeCollection containing MimeType, MimeWildcardType &
     * MimeWildcardSubType Instances.
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

            if (implode('', $typeList[0]) !== $field && $appType) {
                throw new ConNegException('Error parsing field');
            }

            $this->getFromArray($typeCollection, $typeList, $appType);

        } elseif (strlen($field) && $appType) {
            throw new ConNegException('Error parsing field');
        }

        return $typeCollection;
    }
}
