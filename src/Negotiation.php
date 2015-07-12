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

use ptlis\ConNeg\Negotiator\Negotiator;
use ptlis\ConNeg\Parser\FieldParser;
use ptlis\ConNeg\Parser\FieldTokenizer;
use ptlis\ConNeg\Preference\Builder\MimePreferenceBuilder;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilder;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiation
{
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
    private $parser;

    /**
     * Negotiator for non-mime types.
     *
     * @var Negotiator
     */
    private $negotiator;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        $stdTypeBuilder = new PreferenceBuilder();
        $mimeTypeBuilder = new MimePreferenceBuilder();

        $this->tokenizer = new FieldTokenizer();
        $this->parser = new FieldParser($stdTypeBuilder, $mimeTypeBuilder);
        $this->negotiator = new Negotiator($stdTypeBuilder, $mimeTypeBuilder);
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
        return $this->genericBest($userField, $appField, MatchedPreferencesInterface::CHARSET);
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
        return $this->genericAll($userField, $appField, MatchedPreferencesInterface::CHARSET);
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
        return $this->genericBest($userField, $appField, MatchedPreferencesInterface::ENCODING);
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
        return $this->genericAll($userField, $appField, MatchedPreferencesInterface::ENCODING);
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
        return $this->genericBest($userField, $appField, MatchedPreferencesInterface::LANGUAGE);
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
        return $this->genericAll($userField, $appField, MatchedPreferencesInterface::LANGUAGE);
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
        return $this->genericBest($userField, $appField, MatchedPreferencesInterface::MIME);
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
        return $this->genericAll($userField, $appField, MatchedPreferencesInterface::MIME);
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
        $userTypeList = $this->parsePreferences(false, $userField, $fromField);
        $appTypeList = $this->parsePreferences(true, $appField, $fromField);

        $best = $this->negotiator->negotiateBest($userTypeList, $appTypeList, $fromField);

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
        $userTypeList = $this->parsePreferences(false, $userField, $fromField);
        $appTypeList = $this->parsePreferences(true, $appField, $fromField);

        $all = $this->negotiator->negotiateAll($userTypeList, $appTypeList, $fromField);

        return $all;
    }

    /**
     * Parse user preferences and return an array of Preference instances
     *
     * @param bool $appField
     * @param string $field
     * @param string $fromField
     *
     * @return PreferenceInterface[]
     */
    private function parsePreferences($appField, $field, $fromField)
    {
        $tokenList = $this->tokenizer->tokenize($field, $fromField);

        $preferenceList = $this->parser->parse($tokenList, $appField, $fromField);

        return $preferenceList;
    }
}
