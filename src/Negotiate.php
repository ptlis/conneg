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

namespace ptlis\ConNeg;

use ptlis\ConNeg\Collection\CollectionInterface;
use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Exception\ConNegException;
use ptlis\ConNeg\Negotiator\MimeNegotiator;
use ptlis\ConNeg\Negotiator\Negotiator;
use ptlis\ConNeg\RegexProvider\MimeTypeRegexProvider;
use ptlis\ConNeg\RegexProvider\TypeRegexProvider;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\TypeBuilder\MimeTypeBuilder;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;
use ptlis\ConNeg\TypeFactory\MimeTypeFactory;
use ptlis\ConNeg\TypeFactory\TypeFactory;
use ptlis\ConNeg\TypeFactory\TypeFactoryInterface;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\Conneg\TypePair\TypePairInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiate
{
    /**
     * Type factory creating charset types.
     *
     * @var TypeFactory
     */
    private $charsetFactory;

    /**
     * Negotiator for charset types.
     *
     * @var Negotiator
     */
    private $charsetNegotiator;

    /**
     * Type factory creating encoding types.
     *
     * @var TypeFactory
     */
    private $encodingFactory;

    /**
     * Negotiator for encoding types.
     *
     * @var Negotiator
     */
    private $encodingNegotiator;

    /**
     * Type factory creating language types.
     *
     * @var TypeFactory
     */
    private $languageFactory;

    /**
     * Negotiator for language types.
     *
     * @var Negotiator
     */
    private $languageNegotiator;

    /**
     * Type factory creating mime types.
     *
     * @var MimeTypeFactory
     */
    private $mimeFactory;

    /**
     * Negotiator for mime types.
     *
     * @var MimeNegotiator
     */
    private $mimeNegotiator;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        $sharedRegexProvider        = new TypeRegexProvider();
        $qualityFactorFactory       = new QualityFactorFactory();

        // Prepare factories
        $this->charsetFactory       = new TypeFactory(
            $sharedRegexProvider,
            new TypeBuilder($qualityFactorFactory)
        );
        $this->encodingFactory      = new TypeFactory(
            $sharedRegexProvider,
            new TypeBuilder($qualityFactorFactory)
        );
        $this->languageFactory      = new TypeFactory(
            $sharedRegexProvider,
            new TypeBuilder($qualityFactorFactory)
        );
        $this->mimeFactory          = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder($qualityFactorFactory)
        );

        // Prepare pair sorters
        $sharedSort = new TypePairSort(
            new TypePair(
                $this->charsetFactory->get('', '0', false),
                $this->charsetFactory->get('', '0', true)
            )
        );
        $mimeSort = new TypePairSort(
            new TypePair(
                $this->mimeFactory->get('', '0', false),
                $this->mimeFactory->get('', '0', true)
            )
        );

        // Prepare negotiators
        $this->charsetNegotiator    = new Negotiator(
            $this->charsetFactory,
            $sharedSort
        );
        $this->encodingNegotiator   = new Negotiator(
            $this->encodingFactory,
            $sharedSort
        );
        $this->languageNegotiator   = new Negotiator(
            $this->languageFactory,
            $sharedSort
        );
        $this->mimeNegotiator       = new MimeNegotiator(
            $this->mimeFactory,
            $mimeSort
        );
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function charsetBest($userField, $appPrefs)
    {
        $userTypeList = $this->charsetFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->charsetFactory);

        return $this->charsetNegotiator->negotiateBest($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @throws ConNegException
     *
     * @return SharedTypePairCollection containing CharsetType, WildcardType & AbsentType instances.
     */
    public function charsetAll($userField, $appPrefs)
    {
        $userTypeList = $this->charsetFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->charsetFactory);

        return $this->charsetNegotiator->negotiateAll($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function encodingBest($userField, $appPrefs)
    {
        $userTypeList = $this->encodingFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->encodingFactory);

        return $this->encodingNegotiator->negotiateBest($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @throws ConNegException
     *
     * @return SharedTypePairCollection containing EncodingType, WildcardType & AbsentType instances.
     */
    public function encodingAll($userField, $appPrefs)
    {
        $userTypeList = $this->encodingFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->encodingFactory);

        return $this->encodingNegotiator->negotiateAll($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function languageBest($userField, $appPrefs)
    {
        $userTypeList = $this->languageFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->languageFactory);

        return $this->languageNegotiator->negotiateBest($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @throws ConNegException
     *
     * @return SharedTypePairCollection containing LanguageType, WildcardType & AbsentType instances.
     */
    public function languageAll($userField, $appPrefs)
    {
        $userTypeList = $this->languageFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->languageFactory);

        return $this->languageNegotiator->negotiateAll($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function mimeBest($userField, $appPrefs)
    {
        $userTypeList = $this->mimeFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->mimeFactory);

        return $this->mimeNegotiator->negotiateBest($userTypeList, $appTypeList);
    }

    /**
     * Parse the Accept field & negotiate against application types, return an array of types sorted by preference.
     *
     * @param string $userField
     * @param string|TypeCollection $appPrefs
     *
     * @throws ConNegException
     *
     * @return SharedTypePairCollection containing MimeType, MimeWildcardType, MimeWildcardSubType & AbsentType instances.
     */
    public function mimeAll($userField, $appPrefs)
    {
        $userTypeList = $this->mimeFactory->parseUser($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->mimeFactory);

        return $this->mimeNegotiator->negotiateAll($userTypeList, $appTypeList);
    }

    /**
     * Convert application type preferences to a TypeCollection.
     *
     * @throws ConNegException
     *
     * @param string|CollectionInterface $appPrefs
     * @param TypeFactoryInterface  $factory
     *
     * @return TypeCollection
     */
    private function sharedAppPrefsToTypes($appPrefs, TypeFactoryInterface $factory)
    {
        if (gettype($appPrefs) === 'string') {
            $appTypeList = $factory->parseApp($appPrefs);

        } elseif ($appPrefs instanceof CollectionInterface) {
            $appTypeList = $appPrefs;

        } else {
            throw new ConNegException('invalid application preferences passed to ' . __CLASS__ . '::' . __METHOD__);
        }

        return $appTypeList;
    }
}
