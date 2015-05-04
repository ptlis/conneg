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

namespace ptlis\ConNeg;

use ptlis\ConNeg\Collection\CollectionInterface;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Negotiator\MimeNegotiator;
use ptlis\ConNeg\Negotiator\Negotiator;
use ptlis\ConNeg\Parse\FieldParser;
use ptlis\ConNeg\Parse\FieldTokenizer;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\TypeBuilder\MimeTypeBuilder;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\Conneg\TypePair\TypePairInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiation
{
    /**
     * Negotiator for non-mime types.
     *
     * @var Negotiator
     */
    private $stdNegotiator;

    /**
     * Negotiator for mime types.
     *
     * @var MimeNegotiator
     */
    private $mimeNegotiator;

    /**
     * Tokenizer.
     *
     * @var FieldTokenizer
     */
    private $tokenizer;

    /**
     * Parser for non-mime types.
     *
     * @var FieldParser
     */
    private $stdParser;

    /**
     * Parser for mime types.
     *
     * @var FieldParser
     */
    private $mimeParser;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        // Prepare dependencies
        $qualityFactorFactory = new QualityFactorFactory();

        $stdTypeBuilder = new TypeBuilder($qualityFactorFactory);
        $mimeTypeBuilder = new MimeTypeBuilder($qualityFactorFactory);

        $this->tokenizer = new FieldTokenizer();

        $this->stdParser = new FieldParser($stdTypeBuilder, false);
        $this->mimeParser = new FieldParser($mimeTypeBuilder, true);


        // Prepare pair sorters
        $sharedSort = new TypePairSort(
            new TypePair(
                $stdTypeBuilder->getEmpty(),
                $stdTypeBuilder->getEmpty()
            )
        );

        $mimeSort = new TypePairSort(
            new TypePair(
                $mimeTypeBuilder->getEmpty(),
                $mimeTypeBuilder->getEmpty()
            )
        );

        $this->stdNegotiator = new Negotiator(
            $stdTypeBuilder->getEmpty(),
            $sharedSort
        );
        $this->mimeNegotiator       = new MimeNegotiator(
            $mimeTypeBuilder->getEmpty(),
            $mimeSort
        );
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @return TypePairInterface
     */
    public function charsetBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, false);
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @throws ConNegException
     *
     * @return TypePairCollection containing CharsetType, WildcardType & AbsentType instances.
     */
    public function charsetAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, false);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @return TypePairInterface
     */
    public function encodingBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, false);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @throws ConNegException
     *
     * @return TypePairCollection containing EncodingType, WildcardType & AbsentType instances.
     */
    public function encodingAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, false);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @return TypePairInterface
     */
    public function languageBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, false);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @throws ConNegException
     *
     * @return TypePairCollection containing LanguageType, WildcardType & AbsentType instances.
     */
    public function languageAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, false);
    }

    /**
     * Parse the Accept field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @return TypePairInterface
     */
    public function mimeBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, true);
    }

    /**
     * Parse the Accept field & negotiate against application types, returns an array of types sorted by preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     *
     * @throws ConNegException
     *
     * @return TypePairCollection containing MimeType, MimeWildcardType, MimeWildcardSubType & AbsentType
     *          instances.
     */
    public function mimeAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, true);
    }

    /**
     * Shared code to parse an Accept* field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     * @param bool $isMimeField
     *
     * @return TypePair|TypePairInterface
     */
    private function genericBest($userField, $appField, $isMimeField)
    {
        $userTypeList = $this->sharedUserPrefsToTypes($userField, $isMimeField);
        $appTypeList = $this->sharedAppPrefsToTypes($appField, $isMimeField);

        if ($isMimeField) {
            $best = $this->mimeNegotiator->negotiateBest($userTypeList, $appTypeList);
        } else {
            $best = $this->stdNegotiator->negotiateBest($userTypeList, $appTypeList);
        }

        return $best;
    }

    /**
     * Shared code to parse an Accept* field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appField
     * @param bool $isMimeField
     *
     * @return TypePairCollection
     */
    private function genericAll($userField, $appField, $isMimeField)
    {
        $userTypeList = $this->sharedUserPrefsToTypes($userField, $isMimeField);
        $appTypeList = $this->sharedAppPrefsToTypes($appField, $isMimeField);

        if ($isMimeField) {
            $all = $this->mimeNegotiator->negotiateAll($userTypeList, $appTypeList);
        } else {
            $all = $this->stdNegotiator->negotiateAll($userTypeList, $appTypeList);
        }

        return $all;
    }

    /**
     * Takes string serialization of user-agent type preferences and returns a collection of type objects.
     *
     * @param string $userField
     * @param bool $isMimeField
     *
     * @return TypeCollection
     */
    private function sharedUserPrefsToTypes($userField, $isMimeField)
    {
        $tokenList = $this->tokenizer->tokenize($userField, $isMimeField);

        if ($isMimeField) {
            $typeList = $this->mimeParser->parse($tokenList, false);
        } else {
            $typeList = $this->stdParser->parse($tokenList, false);
        }

        return $typeList;
    }

    /**
     * Convert application type preferences to a TypeCollection.
     *
     * @throws ConNegException
     *
     * @param string|CollectionInterface $appField
     * @param bool $isMimeField
     *
     * @return TypeCollection
     */
    private function sharedAppPrefsToTypes($appField, $isMimeField)
    {
        if (gettype($appField) === 'string') {
            $tokenList = $this->tokenizer->tokenize($appField, $isMimeField);
            if ($isMimeField) {
                $appTypeList = $this->mimeParser->parse($tokenList, true);
            } else {
                $appTypeList = $this->stdParser->parse($tokenList, true);
            }

        } elseif ($appField instanceof CollectionInterface) {
            $appTypeList = $appField;

        } else {
            throw new ConNegException('invalid application preferences passed to ' . __METHOD__);
        }

        return $appTypeList;
    }
}
