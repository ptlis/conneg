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

namespace ptlis\ConNeg\Test\Parse;

use ptlis\ConNeg\Parse\FieldTokenizer;

/**
 * Tests to ensure that the tokenizer behaves correctly.
 */
class FieldTokenizerTest extends \PHPUnit_Framework_TestCase
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
        $real = $tokenizer->tokenize($httpField);

        $this->assertEquals($expected, $real);
    }
    public function testTokenizeAcceptCharsetWithSpaces()
    {
        $httpField = 'iso-8859-5 ,utf-8; q= 0.9 , *; q= 0.5';
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
        $real = $tokenizer->tokenize($httpField);

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
        $real = $tokenizer->tokenize($httpField, true);

        $this->assertEquals($expected, $real);
    }
}
