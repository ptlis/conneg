<?php declare(strict_types=1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Parse;

use PHPUnit\Framework\TestCase;
use ptlis\ConNeg\Parser\FieldTokenizer;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Tests to ensure that the tokenizer behaves correctly.
 */
class FieldTokenizerTest extends TestCase
{
    public function testTokenizeAcceptCharset()
    {
        $httpField = 'iso-8859-5,utf-8;q=0.9,*;q=0.5';
        $expected = array(
            'iso-8859-5',
            ',',
            'utf-8',
            ';',
            'q',
            '=',
            '0.9',
            ',',
            '*',
            ';',
            'q',
            '=',
            '0.5'
        );

        $tokenizer = new FieldTokenizer();
        $real = $tokenizer->tokenize($httpField, PreferenceInterface::CHARSET);

        $this->assertEquals($expected, $real);
    }

    public function testTokenizeAcceptCharsetWithSpaces()
    {
        $httpField = 'iso-8859-5 ,utf-8; q= 0.9 , *; q= 0.5 ';
        $expected = array(
            'iso-8859-5',
            ',',
            'utf-8',
            ';',
            'q',
            '=',
            '0.9',
            ',',
            '*',
            ';',
            'q',
            '=',
            '0.5'
        );

        $tokenizer = new FieldTokenizer();
        $real = $tokenizer->tokenize($httpField, PreferenceInterface::CHARSET);

        $this->assertEquals($expected, $real);
    }

    public function testTokenizeAccept()
    {
        $httpField =
            'application/atom+xml;q=0.8;woop="wibble/wobble;,",text/html;q=0.3,application/rss+xml;q=0.5;foo;bar=baz';
        $expected = array(
            'application',
            '/',
            'atom+xml',
            ';',
            'q',
            '=',
            '0.8',
            ';',
            'woop',
            '=',
            'wibble/wobble;,',
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
            ';',
            'q',
            '=',
            '0.5',
            ';',
            'foo',
            ';',
            'bar',
            '=',
            'baz'
        );

        $tokenizer = new FieldTokenizer();
        $real = $tokenizer->tokenize($httpField, PreferenceInterface::MIME);

        $this->assertEquals($expected, $real);
    }

    public function testTokenizePartialLang()
    {
        $httpField = 'es-*;q=0.7,es-ES;q=0.9,es-CO;q=0.8';
        $expected = array(
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
            '0.9',
            ',',
            'es-CO',
            ';',
            'q',
            '=',
            '0.8'
        );

        $tokenizer = new FieldTokenizer();
        $real = $tokenizer->tokenize($httpField, PreferenceInterface::MIME);

        $this->assertEquals($expected, $real);
    }
}
