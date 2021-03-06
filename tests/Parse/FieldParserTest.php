<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Parse;

use PHPUnit\Framework\TestCase;
use ptlis\ConNeg\Exception\InvalidVariantException;
use ptlis\ConNeg\Parser\FieldParser;
use ptlis\ConNeg\Preference\Builder\MimePreferenceBuilder;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilder;
use ptlis\ConNeg\Preference\Preference;

/**
 * Tests to ensure that the parser behaves correctly.
 */
class FieldParserTest extends TestCase
{
    public function testParseAccept()
    {
        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $expected = array(
            new Preference('application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference('text/html', 0.3, Preference::COMPLETE),
            new Preference('application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, true, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseAcceptWithKvpExtens()
    {
        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ';',
            'level',
            '=',
            '1',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $expected = array(
            new Preference('application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference('text/html', 0.3, Preference::COMPLETE),
            new Preference('application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, true, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseAcceptWithValueExtens()
    {
        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ';',
            'level',
            '=',
            '1',
            ';',
            'foo',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $expected = array(
            new Preference('application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference('text/html', 0.3, Preference::COMPLETE),
            new Preference('application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, true, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseAcceptWithQuotedValueExtens()
    {
        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ';',
            'level',
            '=',
            '1',
            ';',
            'foo',
            '=',
            '"bar,;/="',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $expected = array(
            new Preference('application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference('text/html', 0.3, Preference::COMPLETE),
            new Preference('application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, true, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseServerAcceptWithInvalidExtens()
    {
        $this->expectException(InvalidVariantException::class);
        $this->expectExceptionMessage('Invalid count for parameters; expecting 1 or 3, got "2"');

        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ';',
            'level',
            '=',
            '1',
            ';',
            'foo',
            '=',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $parser->parse($mimeTokens, true, Preference::MIME);
    }

    public function testParseClientAcceptWithInvalidExtens()
    {
        $mimeTokens = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ',',
            'text',
            '/',
            'html',
            ';',
            'q',
            '=',
            '0.3',
            ';',
            'level',
            '=',
            '1',
            ';',
            'foo',
            '=',
            ',',
            'application',
            '/',
            'rss+xml',
        );

        $expected = array(
            new Preference('application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference('text/html', 0.3, Preference::COMPLETE),
            new Preference('application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, false, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseServerPartialLang()
    {
        $tokens = array(
            'es-*',
            ';',
            'q',
            '=',
            '0.7',
            ',',
            'es-ES',
            ';',
            'q',
            '=',
            '0.9'
        );

        $expected = array(
            new Preference('es-*', 0.7, Preference::PARTIAL_WILDCARD),
            new Preference('es-ES', 0.9, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($tokens, true, Preference::LANGUAGE);

        $this->assertEquals($expected, $real);
    }

    public function testParseAcceptInvalidType()
    {
        $this->expectException(InvalidVariantException::class);
        $this->expectExceptionMessage('"application/" is not a valid mime type');

        $mimeTokens = array(
            'application',
            '/'
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $parser->parse($mimeTokens, true, Preference::MIME);
    }

    public function testParseAcceptInvalidTypeWithQualityFactor()
    {
        $this->expectException(InvalidVariantException::class);
        $this->expectExceptionMessage('"application/;q=0.8" is not a valid mime type');

        $mimeTokens = array(
            'application',
            '/',
            ';',
            'q',
            '=',
            '0.8'
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $parser->parse($mimeTokens, true, Preference::MIME);
    }

    public function testParseAcceptInvalidParamsCount()
    {
        $this->expectException(InvalidVariantException::class);
        $this->expectExceptionMessage('Invalid count for parameters; expecting 1 or 3, got "2"');

        $mimeTokens = array(
            'application',
            '/',
            'xml',
            ';',
            'foo',
            '='
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $parser->parse($mimeTokens, true, Preference::MIME);
    }

    public function testParseCharset()
    {
        $stdTokens = array(
            'iso-8859-5',
            ',',
            'utf-8',
            ';',
            'q',
            '=',
            '0.9'
        );

        $expected = array(
            new Preference('iso-8859-5', 1, Preference::COMPLETE),
            new Preference('utf-8', 0.9, Preference::COMPLETE),
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($stdTokens, true, Preference::CHARSET);

        $this->assertEquals($expected, $real);
    }

    public function testParseCharsetDoubledParamsSeparator()
    {
        $stdTokens = array(
            'iso-8859-5',
            ',',
            'utf-8',
            ';',
            ';',
            'q',
            '=',
            '0.9'
        );

        $expected = array(
            new Preference('iso-8859-5', 1, Preference::COMPLETE),
            new Preference('utf-8', 0.9, Preference::COMPLETE),
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($stdTokens, true, Preference::CHARSET);

        $this->assertEquals($expected, $real);
    }

    public function testParseCharsetDoubledTypeSeparator()
    {
        $stdTokens = array(
            'iso-8859-5',
            ',',
            ',',
            'utf-8',
            ';',
            'q',
            '=',
            '0.9'
        );

        $expected = array(
            new Preference('iso-8859-5', 1, Preference::COMPLETE),
            new Preference('utf-8', 0.9, Preference::COMPLETE),
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($stdTokens, true, Preference::CHARSET);

        $this->assertEquals($expected, $real);
    }
}
