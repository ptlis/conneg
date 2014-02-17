<?php

/**
 * Class providing a simple API through which content negotiation is performed.
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

namespace ptlis\ConNeg;

use Exception;
use ptlis\ConNeg\Collection\CollectionInterface;
use ptlis\ConNeg\Collection\MimeTypePairCollection;
use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Negotiator\CharsetNegotiator;
use ptlis\ConNeg\Negotiator\EncodingNegotiator;
use ptlis\ConNeg\Negotiator\LanguageNegotiator;
use ptlis\ConNeg\Negotiator\MimeNegotiator;
use ptlis\ConNeg\Negotiator\SharedNegotiator;
use ptlis\ConNeg\Type\Charset\CharsetTypeFactory;
use ptlis\ConNeg\Type\Encoding\EncodingTypeFactory;
use ptlis\ConNeg\Type\Language\LanguageTypeFactory;
use ptlis\ConNeg\Type\Mime\MimeTypeFactory;
use ptlis\ConNeg\Type\TypeFactoryInterface;
use ptlis\Conneg\TypePair\TypePairInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiate
{
    /**
     * @var CharsetTypeFactory
     */
    private $charsetFactory;

    /**
     * @var CharsetNegotiator
     */
    private $charsetNegotiator;

    /**
     * @var EncodingTypeFactory
     */
    private $encodingFactory;

    /**
     * @var EncodingNegotiator
     */
    private $encodingNegotiator;

    /**
     * @var LanguageTypeFactory
     */
    private $languageFactory;

    /**
     * @var LanguageNegotiator
     */
    private $languageNegotiator;

    /**
     * @var MimeTypeFactory
     */
    private $mimeFactory;

    /**
     * @var MimeNegotiator
     */
    private $mimeNegotiator;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        $regexProvider              = new RegexProvider();

        $this->charsetFactory       = new CharsetTypeFactory($regexProvider);
        $this->charsetNegotiator    = new CharsetNegotiator(new SharedNegotiator($this->charsetFactory));

        $this->encodingFactory      = new EncodingTypeFactory($regexProvider);
        $this->encodingNegotiator   = new EncodingNegotiator(new SharedNegotiator($this->encodingFactory));

        $this->languageFactory      = new LanguageTypeFactory($regexProvider);
        $this->languageNegotiator   = new LanguageNegotiator(new SharedNegotiator($this->languageFactory));

        $this->mimeFactory          = new MimeTypeFactory($regexProvider);
        $this->mimeNegotiator       = new MimeNegotiator($this->mimeFactory);
    }


    /**
     * Parse the Accept-Charset field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function charsetBest($userField, $appPrefs)
    {
        $userTypeList = $this->charsetFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->charsetFactory);

        return $this->charsetNegotiator->negotiateBest($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept-Charset field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @throws Exception
     *
     * @return SharedTypePairCollection containing CharsetType, WildcardType & AbsentType instances.
     */
    public function charsetAll($userField, $appPrefs)
    {
        $userTypeList = $this->charsetFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->charsetFactory);

        return $this->charsetNegotiator->negotiateAll($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept-Encoding field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function encodingBest($userField, $appPrefs)
    {
        $userTypeList = $this->encodingFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->encodingFactory);

        return $this->encodingNegotiator->negotiateBest($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept-Encoding field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @throws Exception
     *
     * @return SharedTypePairCollection containing EncodingType, WildcardType & AbsentType instances.
     */
    public function encodingAll($userField, $appPrefs)
    {
        $userTypeList = $this->encodingFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->encodingFactory);

        return $this->encodingNegotiator->negotiateAll($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept-Language field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function languageBest($userField, $appPrefs)
    {
        $userTypeList = $this->languageFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->languageFactory);

        return $this->languageNegotiator->negotiateBest($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept-Language field & negotiate against application types, return an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string|SharedTypePairCollection $appPrefs
     *
     * @throws Exception
     *
     * @return SharedTypePairCollection containing LanguageType, WildcardType & AbsentType instances.
     */
    public function languageAll($userField, $appPrefs)
    {
        $userTypeList = $this->languageFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->languageFactory);

        return $this->languageNegotiator->negotiateAll($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept field & negotiate against application types, return the preferred type.
     *
     * @param string $userField
     * @param string|MimeTypePairCollection $appPrefs
     *
     * @return TypePairInterface
     */
    public function mimeBest($userField, $appPrefs)
    {
        $userTypeList = $this->mimeFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->mimeFactory);

        return $this->mimeNegotiator->negotiateBest($userTypeList, $appTypeList);
    }


    /**
     * Parse the Accept field & negotiate against application types, return an array of types sorted by preference.
     *
     * @param string $userField
     * @param string|MimeTypePairCollection $appPrefs
     *
     * @throws Exception
     *
     * @return MimeTypePairCollection containing MimeType, MimeWildcardType, MimeWildcardSubType & AbsentType instances.
     */
    public function mimeAll($userField, $appPrefs)
    {
        $userTypeList = $this->mimeFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->mimeFactory);

        return $this->mimeNegotiator->negotiateAll($userTypeList, $appTypeList);
    }


    /**
     * Convert application type preferences to a TypeCollection.
     *
     * @throws \Exception
     *
     * @param string|CollectionInterface $appPrefs
     * @param TypeFactoryInterface  $factory
     *
     * @return CollectionInterface
     */
    private function sharedAppPrefsToTypes($appPrefs, TypeFactoryInterface $factory)
    {
        if (gettype($appPrefs) === 'string') {
            $appTypeList = $factory->parse($appPrefs);

        } elseif ($appPrefs instanceof CollectionInterface) {
            $appTypeList = $appPrefs;

        } else {
            // TODO: Throw appropriate exception
            throw new Exception('invalid application preferences passed to ' . __CLASS__ . '::' . __METHOD__);
        }

        return $appTypeList;
    }
}
