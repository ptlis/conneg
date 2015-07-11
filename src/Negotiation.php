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

use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Matcher\MimeMatcher;
use ptlis\ConNeg\Matcher\Matcher;
use ptlis\ConNeg\Parser\FieldParser;
use ptlis\ConNeg\Parser\FieldTokenizer;
use ptlis\ConNeg\Preference\Builder\MimePreferenceBuilder;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilder;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiation
{
    /**
     * Negotiator for non-mime types.
     *
     * @var Matcher
     */
    private $stdNegotiator;

    /**
     * Negotiator for mime types.
     *
     * @var MimeMatcher
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
        $stdTypeBuilder = new PreferenceBuilder();
        $mimeTypeBuilder = new MimePreferenceBuilder();

        $this->tokenizer = new FieldTokenizer();

        $this->stdParser = new FieldParser($stdTypeBuilder);
        $this->mimeParser = new FieldParser($mimeTypeBuilder);


        $this->stdNegotiator = new Matcher($stdTypeBuilder);
        $this->mimeNegotiator = new MimeMatcher($mimeTypeBuilder);
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string $appField
     *
     * @return MatchedPreferencesInterface
     */
    public function charsetBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, PreferenceInterface::CHARSET);
    }

    /**
     * Parse the Accept-Charset field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string $appField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferencesCollection containing CharsetType, WildcardType & AbsentType instances.
     */
    public function charsetAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, PreferenceInterface::CHARSET);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string $appField
     *
     * @return MatchedPreferencesInterface
     */
    public function encodingBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, PreferenceInterface::ENCODING);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string $appField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferencesCollection containing EncodingType, WildcardType & AbsentType instances.
     */
    public function encodingAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, PreferenceInterface::ENCODING);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string $appField
     *
     * @return MatchedPreferencesInterface
     */
    public function languageBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, PreferenceInterface::LANGUAGE);
    }

    /**
     * Parse the Accept-Language field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string $appField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferencesCollection containing LanguageType, WildcardType & AbsentType instances.
     */
    public function languageAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, PreferenceInterface::LANGUAGE);
    }

    /**
     * Parse the Accept field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string $appField
     *
     * @return MatchedPreferencesInterface
     */
    public function mimeBest($userField, $appField)
    {
        return $this->genericBest($userField, $appField, PreferenceInterface::MIME);
    }

    /**
     * Parse the Accept field & negotiate against application types, returns an array of types sorted by preference.
     *
     * @param string $userField
     * @param string $appField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferencesCollection containing MimeType, MimeWildcardType, MimeWildcardSubType & AbsentType
     *          instances.
     */
    public function mimeAll($userField, $appField)
    {
        return $this->genericAll($userField, $appField, PreferenceInterface::MIME);
    }

    /**
     * Shared code to parse an Accept* field & negotiate against application types, returns the preferred type.
     *
     * @param string $userField
     * @param string $appField
     * @param string $fromField
     *
     * @return MatchedPreferences|MatchedPreferencesInterface
     */
    private function genericBest($userField, $appField, $fromField)
    {
        $userTypeList = $this->sharedUserPrefsToTypes($userField, $fromField);
        $appTypeList = $this->sharedAppPrefsToTypes($appField, $fromField);

        if (PreferenceInterface::MIME === $fromField) {
            $best = $this->mimeNegotiator->negotiateBest($userTypeList, $appTypeList, $fromField);

        } else {
            $best = $this->stdNegotiator->negotiateBest($userTypeList, $appTypeList, $fromField);
        }

        return $best;
    }

    /**
     * Shared code to parse an Accept* field & negotiate against application types, returns an array of types sorted by
     * preference.
     *
     * @param string $userField
     * @param string $appField
     * @param string $fromField
     *
     * @return MatchedPreferencesCollection
     */
    private function genericAll($userField, $appField, $fromField)
    {
        $userTypeList = $this->sharedUserPrefsToTypes($userField, $fromField);
        $appTypeList = $this->sharedAppPrefsToTypes($appField, $fromField);

        if (PreferenceInterface::MIME === $fromField) {
            $all = $this->mimeNegotiator->negotiateAll($userTypeList, $appTypeList, $fromField);

        } else {
            $all = $this->stdNegotiator->negotiateAll($userTypeList, $appTypeList, $fromField);
        }

        return $all;
    }

    /**
     * Takes string serialization of user-agent type preferences and returns a collection of type objects.
     *
     * @param string $userField
     * @param string $fromField
     *
     * @return PreferenceInterface[]
     */
    private function sharedUserPrefsToTypes($userField, $fromField)
    {
        $tokenList = $this->tokenizer->tokenize($userField, $fromField);

        if (PreferenceInterface::MIME === $fromField) {
            $typeList = $this->mimeParser->parse($tokenList, false, $fromField);

        } else {
            $typeList = $this->stdParser->parse($tokenList, false, $fromField);
        }

        return $typeList;
    }

    /**
     * Convert application type preferences to a TypeCollection.
     *
     * @throws \LogicException
     *
     * @param string $appField
     * @param string $fromField
     *
     * @return PreferenceInterface[]
     */
    private function sharedAppPrefsToTypes($appField, $fromField)
    {
        if (gettype($appField) === 'string') {
            $tokenList = $this->tokenizer->tokenize($appField, $fromField);
            if (PreferenceInterface::MIME === $fromField) {
                $appTypeList = $this->mimeParser->parse($tokenList, true, $fromField);

            } else {
                $appTypeList = $this->stdParser->parse($tokenList, true, $fromField);
            }

        } else {
            throw new \LogicException('Invalid application preferences passed to ' . __METHOD__);
        }

        return $appTypeList;
    }
}
