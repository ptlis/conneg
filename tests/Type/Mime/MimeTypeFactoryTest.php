<?php

/**
 * Test to verify the correctness of MimeTypeFactory.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Type\Charset;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\RegexProvider;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\Type\Mime\MimeTypeFactory;
use ptlis\ConNeg\Type\Mime\MimeWildcardSubType;
use ptlis\ConNeg\Type\Mime\MimeWildcardType;

class MimeTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'text/html';
        $qFactor = 1;

        $expectType = new MimeType('text', 'html', new QualityFactor($qFactor));

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'text/html;q=0.75';

        $expectType = new MimeType('text', 'html', new QualityFactor(0.75));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'application/xml';

        $expectType = new MimeType('application', 'xml', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardTypeIncludeQualityFactor()
    {
        $field = '*/*;q=0.5';

        $expectType = new MimeWildcardType(new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardTypeOmitQualityFactor()
    {
        $field = '*/*';

        $expectType = new MimeWildcardType(new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardSubTypeIncludeQualityFactor()
    {
        $field = 'text/*;q=0.5';

        $expectType = new MimeWildcardSubType('text', new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardSubTypeOmitQualityFactor()
    {
        $field = 'application/*';

        $expectType = new MimeWildcardSubType('application', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'text/html,application/xml+rdf;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('application', 'xml+rdf', new QualityFactor(0.7)));

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'text/html;q=0.9,text/*;q=0.5, */*;q=0.1';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(0.9)));
        $expectCollection->addType(new MimeWildcardSubType('text', new QualityFactor(0.5)));
        $expectCollection->addType(new MimeWildcardType(new QualityFactor(0.1)));

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSpecExampleOne()
    {
        $field = 'audio/*; q=0.2, audio/basic';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeWildcardSubType('audio', new QualityFactor(0.2)));
        $expectCollection->addType(new MimeType('audio', 'basic', new QualityFactor(1)));

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSpecExampleTwo()
    {
        $field = 'text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'plain', new QualityFactor(0.5)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'x-dvi', new QualityFactor(0.8)));
        $expectCollection->addType(new MimeType('text', 'x-c', new QualityFactor(1)));

        $factory = new MimeTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }
}
