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
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Negotiator\CharsetNegotiator;
use ptlis\ConNeg\Negotiator\EncodingNegotiator;
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
     * @var CharsetNegotiator
     */
    private $charsetNegotiator;

    /**
     * @var CharsetTypeFactory
     */
    private $charsetFactory;

    /**
     * @var EncodingNegotiator
     */
    private $encodingNegotiator;

    /**
     * @var EncodingTypeFactory
     */
    private $encodingFactory;

    /**
     * @var LanguageTypeFactory
     */
    private $languageFactory;

    /**
     * @var MimeTypeFactory
     */
    private $mimeFactory;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        $regexProvider = new RegexProvider();
        $sharedNegotiator = new SharedNegotiator();

        $this->charsetFactory = new CharsetTypeFactory($regexProvider);
        $this->charsetNegotiator = new CharsetNegotiator($sharedNegotiator);

        $this->encodingFactory = new EncodingTypeFactory($regexProvider);
        $this->encodingNegotiator = new EncodingNegotiator($sharedNegotiator);


        $this->languageFactory = new LanguageTypeFactory($regexProvider);
        $this->mimeFactory = new MimeTypeFactory($regexProvider);
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
        $userTypeList = $this->charsetFactory->parse($userField);
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
     * @throws Exception
     *
     * @return TypePairCollection containing CharsetType, WildcardType & AbsentType instances.
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
     * @param string|TypeCollection $appPrefs
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
     * @param string|TypeCollection $appPrefs
     *
     * @throws Exception
     *
     * @return TypePairCollection containing EncodingType, WildcardType & AbsentType instances.
     */
    public function encodingAll($userField, $appPrefs)
    {
        $userTypeList = $this->encodingFactory->parse($userField);
        $appTypeList = $this->sharedAppPrefsToTypes($appPrefs, $this->encodingFactory);

        return $this->encodingNegotiator->negotiateAll($userTypeList, $appTypeList);
    }


    public function languageBest($userField, $appPrefs)
    {
    }


    public function languageAll($userField, $appPrefs)
    {
    }


    public function mimeBest($userField, $appPrefs)
    {
    }


    public function mimeAll($userField, $appPrefs)
    {
    }


    /**
     * Convert application type preferences to a TypeCollection.
     *
     * @throws \Exception
     *
     * @param string|TypeCollection $appPrefs
     * @param TypeFactoryInterface  $factory
     *
     * @return TypeCollection
     */
    private function sharedAppPrefsToTypes($appPrefs, TypeFactoryInterface $factory)
    {
        if (gettype($appPrefs) === 'string') {
            $appTypeList = $factory->parse($appPrefs);

        } elseif ($appPrefs instanceof TypeCollection) {
            $appTypeList = $appPrefs;

        } else {
            // TODO: Throw appropriate exception
            throw new Exception('invalid application preferences passed to ' . __CLASS__ . '::' . __METHOD__);
        }

        return $appTypeList;
    }
}
