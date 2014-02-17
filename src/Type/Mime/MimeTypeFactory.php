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
use ptlis\ConNeg\QualityFactor\QualityFactor;
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
     * Constructor
     *
     * @param MimeRegexProviderInterface $regex
     */
    public function __construct(MimeRegexProviderInterface $regex)
    {
        $this->regexProvider = $regex;
    }


    /**
     * Parse the provided Accept field & return a TypeCollection containing MimeType, MimeWildcardType &
     * MimeWildcardSubType Instances.
     *
     * @param string $field
     *
     * @return TypeCollection
     */
    public function parse($field)
    {
        $typeCollection = new TypeCollection();

        if (preg_match_all($this->regexProvider->getMimeRegex(), $field, $typeList)) {
            $this->getFromArray($typeCollection, $typeList);

        } else {
            // TODO: Throw exception
        }

        return $typeCollection;
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
     * @param string $type
     * @param string $qualityFactor
     *
     * @return MimeType|MimeWildcardSubtype|MimeWildcardType|AbsentMimeType
     */
    public function get($type, $qualityFactor)
    {
        if (strlen($type)) {
            // TODO: what if no slash??
            list($mimeType, $subType) = explode('/', $type);

            // TODO: what if odd wildcard (eg */html)
            switch (true) {
                // Full wildcard type
                case $mimeType === '*' && $subType === '*':
                    $typeObj = new MimeWildcardType(new QualityFactor($qualityFactor));
                    break;

                // Wildcard subtype
                case $mimeType !== '*' && $subType === '*':
                    $typeObj = new MimeWildcardSubType($mimeType, new QualityFactor($qualityFactor));
                    break;

                default:
                    $typeObj = new MimeType($mimeType, $subType, new QualityFactor($qualityFactor));
                    break;
            }

        } else {
            $typeObj = new AbsentMimeType();
        }

        return $typeObj;
    }
}
