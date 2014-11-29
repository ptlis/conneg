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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Parse\FieldParser;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypeBuilder\MimeTypeBuilder;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;

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

        $expected = new TypeCollection();
        $expected->setList(array(
            new MimeType('application', 'atom+xml', new QualityFactor('0.8')),
            new MimeType('text', 'html', new QualityFactor('0.3')),
            new MimeType('application', 'rss+xml', new QualityFactor('1'))
        ));

        $builder = new MimeTypeBuilder(new QualityFactorFactory());
        $parser = new FieldParser($builder, true);

        $real = $parser->parse($mimeTokens, true);

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

        $builder = new MimeTypeBuilder(new QualityFactorFactory());
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

        $builder = new MimeTypeBuilder(new QualityFactorFactory());
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

        $builder = new MimeTypeBuilder(new QualityFactorFactory());
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

        $expected = new TypeCollection();
        $expected->setList(array(
            new Type('iso-8859-5', new QualityFactor('1')),
            new Type('utf-8', new QualityFactor('0.9')),
        ));

        $builder = new TypeBuilder(new QualityFactorFactory());
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

        $expected = new TypeCollection();
        $expected->setList(array(
            new Type('iso-8859-5', new QualityFactor('1')),
            new Type('utf-8', new QualityFactor('0.9')),
        ));

        $builder = new TypeBuilder(new QualityFactorFactory());
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

        $expected = new TypeCollection();
        $expected->setList(array(
            new Type('iso-8859-5', new QualityFactor('1')),
            new Type('utf-8', new QualityFactor('0.9')),
        ));

        $builder = new TypeBuilder(new QualityFactorFactory());
        $parser = new FieldParser($builder, false);

        $real = $parser->parse($stdTokens, true);

        $this->assertEquals($expected, $real);
    }

    public function testInvalidQualityFactorTooLargeOne()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "1.5" provided, must be between 0 and 1 (inclusive)'
        );

        $stdTokens = array(
            'iso-8859-5',
            ';',
            'q',
            '=',
            '1.5'
        );

        $builder = new TypeBuilder(new QualityFactorFactory());
        $parser = new FieldParser($builder, false);

        $parser->parse($stdTokens, true);
    }

    public function testInvalidQualityFactorTooLargeTwo()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "15" provided, must be between 0 and 1 (inclusive)'
        );

        $stdTokens = array(
            'iso-8859-5',
            ';',
            'q',
            '=',
            '15'
        );

        $builder = new TypeBuilder(new QualityFactorFactory());
        $parser = new FieldParser($builder, false);

        $parser->parse($stdTokens, true);
    }

    public function testInvalidQualityFactorNonNumeric()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "bob" provided, must be between 0 and 1 (inclusive)'
        );

        $stdTokens = array(
            'iso-8859-5',
            ';',
            'q',
            '=',
            'bob'
        );

        $builder = new TypeBuilder(new QualityFactorFactory());
        $parser = new FieldParser($builder, false);

        $parser->parse($stdTokens, true);
    }
}
