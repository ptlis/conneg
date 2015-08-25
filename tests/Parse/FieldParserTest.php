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

namespace ptlis\ConNeg\Test\Parse;

use ptlis\ConNeg\Parser\FieldParser;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Bob\MimePreferenceBuilder;
use ptlis\ConNeg\Preference\Bob\PreferenceBuilder;

/**
 * Tests to ensure that the parser behaves correctly.
 */
class FieldParserTest extends \PHPUnit_Framework_TestCase
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
            new Preference(Preference::MIME, 'application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
            new Preference(Preference::MIME, 'application/rss+xml', 1, Preference::COMPLETE)
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
            new Preference(Preference::MIME, 'application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
            new Preference(Preference::MIME, 'application/rss+xml', 1, Preference::COMPLETE)
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
            new Preference(Preference::MIME, 'application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
            new Preference(Preference::MIME, 'application/rss+xml', 1, Preference::COMPLETE)
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
            new Preference(Preference::MIME, 'application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
            new Preference(Preference::MIME, 'application/rss+xml', 1, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($mimeTokens, true, Preference::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testParseServerAcceptWithInvalidExtens()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid count for parameters; expecting 1 or 3, got "2"'
        );

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
            new Preference(Preference::MIME, 'application/atom+xml', 0.8, Preference::COMPLETE),
            new Preference(Preference::MIME, 'text/html', 0.3, Preference::COMPLETE),
            new Preference(Preference::MIME, 'application/rss+xml', 1, Preference::COMPLETE)
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
            new Preference(Preference::LANGUAGE, 'es-*', 0.7, Preference::PARTIAL_WILDCARD),
            new Preference(Preference::LANGUAGE, 'es-ES', 0.9, Preference::COMPLETE)
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($tokens, true, Preference::LANGUAGE);

        $this->assertEquals($expected, $real);
    }

    public function testParseAcceptInvalidType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"application/" is not a valid mime type'
        );

        $mimeTokens = array(
            'application',
            '/'
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $parser->parse($mimeTokens, true, Preference::MIME);
    }

    public function testParseAcceptInvalidTypeWithQualityFactor()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"application/;q=0.8" is not a valid mime type'
        );

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
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid count for parameters; expecting 1 or 3, got "2"'
        );

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
            new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE),
            new Preference(Preference::CHARSET, 'utf-8', 0.9, Preference::COMPLETE),
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
            new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE),
            new Preference(Preference::CHARSET, 'utf-8', 0.9, Preference::COMPLETE),
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
            new Preference(Preference::CHARSET, 'iso-8859-5', 1, Preference::COMPLETE),
            new Preference(Preference::CHARSET, 'utf-8', 0.9, Preference::COMPLETE),
        );

        $parser = new FieldParser(new PreferenceBuilder(), new MimePreferenceBuilder());

        $real = $parser->parse($stdTokens, true, Preference::CHARSET);

        $this->assertEquals($expected, $real);
    }
}
