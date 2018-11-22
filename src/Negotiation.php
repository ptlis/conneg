<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
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
final class Negotiation
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
    public function charsetBest(string $clientField, string $serverField): string
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
    public function charsetAll(string $clientField, string $serverField): array
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
    public function encodingBest(string $clientField, string $serverField): string
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
    public function encodingAll(string $clientField, string $serverField): array
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
    public function languageBest(string $clientField, string $serverField): string
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
    public function languageAll(string $clientField, string $serverField): array
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
    public function mimeBest(string $clientField, string $serverField): string
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
    public function mimeAll(string $clientField, string $serverField): array
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
    private function genericBest(string $clientField, string $serverField, string $fromField): string
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
    private function genericAll(string $clientField, string $serverField, string $fromField): array
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
    private function parsePreferences(bool $serverField, string $field, string $fromField): array
    {
        $tokenList = $this->tokenizer->tokenize($field, $fromField);

        return $this->parser->parse($tokenList, $serverField, $fromField);
    }
}
