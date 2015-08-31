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
use ptlis\ConNeg\Preference\Matched\MatchedPreference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferenceInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Class providing a simple API through which content negotiation is performed.
 */
class Negotiation
{
    /**
     * @var FieldTokenizer
     */
    private $tokenizer;

    /**
     * @var FieldParser
     */
    private $parser;

    /**
     * @var Negotiator
     */
    private $negotiator;


    /**
     * Constructor, initialise factories.
     */
    public function __construct()
    {
        $prefBuilder = new PreferenceBuilder();
        $mimePrefBuilder = new MimePreferenceBuilder();

        $this->tokenizer = new FieldTokenizer();
        $this->parser = new FieldParser($prefBuilder, $mimePrefBuilder);
        $this->negotiator = new Negotiator($prefBuilder, $mimePrefBuilder);
    }

    /**
     * Parse the Accept-Charset field & negotiate against server preferences, returns the preferred variant.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @return string
     */
    public function charsetBest($clientField, $serverField)
    {
        return $this->genericBest($clientField, $serverField, MatchedPreferenceInterface::CHARSET);
    }

    /**
     * Parse the Accept-Charset field & negotiate against server preferences, returns a sorted array of preferences.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferenceInterface[]
     */
    public function charsetAll($clientField, $serverField)
    {
        return $this->genericAll($clientField, $serverField, MatchedPreferenceInterface::CHARSET);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against server preferences, returns the preferred variant.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @return string
     */
    public function encodingBest($clientField, $serverField)
    {
        return $this->genericBest($clientField, $serverField, MatchedPreferenceInterface::ENCODING);
    }

    /**
     * Parse the Accept-Encoding field & negotiate against server preferences, returns a sorted array of preferences.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferenceInterface[]
     */
    public function encodingAll($clientField, $serverField)
    {
        return $this->genericAll($clientField, $serverField, MatchedPreferenceInterface::ENCODING);
    }

    /**
     * Parse the Accept-Language field & negotiate against server preferences, returns the preferred variant.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @return string
     */
    public function languageBest($clientField, $serverField)
    {
        return $this->genericBest($clientField, $serverField, MatchedPreferenceInterface::LANGUAGE);
    }

    /**
     * Parse the Accept-Language field & negotiate against server preferences, returns a sorted array of preferences.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferenceInterface[]
     */
    public function languageAll($clientField, $serverField)
    {
        return $this->genericAll($clientField, $serverField, MatchedPreferenceInterface::LANGUAGE);
    }

    /**
     * Parse the Accept field & negotiate against server preferences, returns the preferred variant.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @return string
     */
    public function mimeBest($clientField, $serverField)
    {
        return $this->genericBest($clientField, $serverField, MatchedPreferenceInterface::MIME);
    }

    /**
     * Parse the Accept field & negotiate against server preferences, returns a sorted array of preferences.
     *
     * @param string $clientField
     * @param string $serverField
     *
     * @throws \LogicException
     *
     * @return MatchedPreferenceInterface[]
     */
    public function mimeAll($clientField, $serverField)
    {
        return $this->genericAll($clientField, $serverField, MatchedPreferenceInterface::MIME);
    }

    /**
     * Shared code to parse an Accept* field & negotiate against server preferences, returns the preferred variant.
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $fromField
     *
     * @return string
     */
    private function genericBest($clientField, $serverField, $fromField)
    {
        $clientPrefList = $this->parsePreferences(false, $clientField, $fromField);
        $serverPrefList = $this->parsePreferences(true, $serverField, $fromField);

        return $this->negotiator->negotiateBest($clientPrefList, $serverPrefList, $fromField)->getVariant();
    }

    /**
     * Shared code to parse an Accept* field & negotiate against server preferences, returns an array of preferences.
     *
     * @param string $clientField
     * @param string $serverField
     * @param string $fromField
     *
     * @return MatchedPreferenceInterface[]
     */
    private function genericAll($clientField, $serverField, $fromField)
    {
        $clientPrefList = $this->parsePreferences(false, $clientField, $fromField);
        $serverPrefList = $this->parsePreferences(true, $serverField, $fromField);

        return $this->negotiator->negotiateAll($clientPrefList, $serverPrefList, $fromField);
    }

    /**
     * Parse client preferences and return an array of Preference instances
     *
     * @param bool $serverField
     * @param string $field
     * @param string $fromField
     *
     * @return PreferenceInterface[]
     */
    private function parsePreferences($serverField, $field, $fromField)
    {
        $tokenList = $this->tokenizer->tokenize($field, $fromField);

        return $this->parser->parse($tokenList, $serverField, $fromField);
    }
}
