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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Parse\FieldParser;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\Builder\MimeTypeBuilder;
use ptlis\ConNeg\Type\Builder\TypeBuilder;

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

        $expected = new TypeCollection(array(
            new Type('application/atom+xml', 0.8, Type::EXACT_TYPE),
            new Type('text/html', 0.3, Type::EXACT_TYPE),
            new Type('application/rss+xml', 1, Type::EXACT_TYPE)
        ));

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, true);

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

        $expected = new TypeCollection(array(
            new Type('application/atom+xml', 0.8, Type::EXACT_TYPE),
            new Type('text/html', 0.3, Type::EXACT_TYPE),
            new Type('application/rss+xml', 1, Type::EXACT_TYPE)
        ));

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, true);

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

        $expected = new TypeCollection(array(
            new Type('application/atom+xml', 0.8, Type::EXACT_TYPE),
            new Type('text/html', 0.3, Type::EXACT_TYPE),
            new Type('application/rss+xml', 1, Type::EXACT_TYPE)
        ));

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, true);

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

        $expected = new TypeCollection(array(
            new Type('application/atom+xml', 0.8, Type::EXACT_TYPE),
            new Type('text/html', 0.3, Type::EXACT_TYPE),
            new Type('application/rss+xml', 1, Type::EXACT_TYPE)
        ));

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, true);

        $this->assertEquals($expected, $real);
    }

    public function testParseAppAcceptWithInvalidExtens()
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

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $parser->parse($mimeTokens, true);
    }

    public function testParseUserAcceptWithInvalidExtens()
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

        $expected = new TypeCollection(array(
            new Type('application/atom+xml', 0.8, Type::EXACT_TYPE),
            new Type('text/html', 0.3, Type::EXACT_TYPE),
            new Type('application/rss+xml', 1, Type::EXACT_TYPE)
        ));

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, false);

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

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $parser->parse($mimeTokens, true);
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

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $parser->parse($mimeTokens, true);
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

        $builder = new MimeTypeBuilder();
        $parser = new FieldParser($builder, true);

        $parser->parse($mimeTokens, true);
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

        $expected = new TypeCollection(array(
            new Type('iso-8859-5', 1, Type::EXACT_TYPE),
            new Type('utf-8', 0.9, Type::EXACT_TYPE),
        ));

        $builder = new TypeBuilder();
        $parser = new FieldParser($builder, false);

        $real = $parser->parse($stdTokens, true);

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

        $expected = new TypeCollection(array(
            new Type('iso-8859-5', 1, Type::EXACT_TYPE),
            new Type('utf-8', 0.9, Type::EXACT_TYPE),
        ));

        $builder = new TypeBuilder();
        $parser = new FieldParser($builder, false);

        $real = $parser->parse($stdTokens, true);

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

        $expected = new TypeCollection(array(
            new Type('iso-8859-5', 1, Type::EXACT_TYPE),
            new Type('utf-8', 0.9, Type::EXACT_TYPE),
        ));

        $builder = new TypeBuilder();
        $parser = new FieldParser($builder, false);

        $real = $parser->parse($stdTokens, true);

        $this->assertEquals($expected, $real);
    }
}
