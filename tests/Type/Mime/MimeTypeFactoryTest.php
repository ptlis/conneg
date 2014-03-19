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

namespace ptlis\ConNeg\Test\Type\Mime;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\Type\Mime\MimeTypeBuilder;
use ptlis\ConNeg\Type\Mime\MimeTypeFactory;
use ptlis\ConNeg\Type\Mime\MimeTypeMimeTypeRegexProvider;
use ptlis\ConNeg\Type\Mime\MimeTypeRegexProvider;
use ptlis\ConNeg\Type\Mime\MimeWildcardSubType;
use ptlis\ConNeg\Type\Mime\MimeWildcardType;

class MimeTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'text/html';
        $qFactor = 1;

        $expectType = new MimeType('text', 'html', new QualityFactor($qFactor));

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'text/html;q=0.75';

        $expectType = new MimeType('text', 'html', new QualityFactor(0.75));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'application/xml';

        $expectType = new MimeType('application', 'xml', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeIncludeQualityFactor()
    {
        $field = '*/*;q=0.5';

        $expectType = new MimeWildcardType(new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeOmitQualityFactor()
    {
        $field = '*/*';

        $expectType = new MimeWildcardType(new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardSubTypeIncludeQualityFactor()
    {
        $field = 'text/*;q=0.5';

        $expectType = new MimeWildcardSubType('text', new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardSubTypeOmitQualityFactor()
    {
        $field = 'application/*';

        $expectType = new MimeWildcardSubType('application', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'text/html,application/xml+rdf;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('application', 'xml+rdf', new QualityFactor(0.7)));

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'text/html;q=0.9,text/*;q=0.5, */*;q=0.1';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(0.9)));
        $expectCollection->addType(new MimeWildcardSubType('text', new QualityFactor(0.5)));
        $expectCollection->addType(new MimeWildcardType(new QualityFactor(0.1)));

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleOne()
    {
        $field = 'audio/*; q=0.2, audio/basic';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeWildcardSubType('audio', new QualityFactor(0.2)));
        $expectCollection->addType(new MimeType('audio', 'basic', new QualityFactor(1)));

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleTwo()
    {
        $field = 'text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'plain', new QualityFactor(0.5)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'x-dvi', new QualityFactor(0.8)));
        $expectCollection->addType(new MimeType('text', 'x-c', new QualityFactor(1)));

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseAppInvalidTypeOne()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Error parsing field'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('$^(£$');
    }


    public function testParseAppInvalidTypeTwo()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Error parsing field'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('text/html,$^(£$');
    }


    public function testParseAppInvalidTypeThree()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Error parsing field'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('text/html,bob');
    }


    public function testParseUserInvalidType()
    {
        $expectCollection = new TypeCollection();

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser('$^(£$'));
    }


    public function testParseUserInvalidTypeTwo()
    {
        $expectType = new MimeType('text', 'html', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser('text/html,$^(£$'));
    }


    public function testParseUserInvalidTypeThree()
    {
        $expectCollection = new TypeCollection();

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser('*/html'));
    }


    public function testParseUserInvalidTypeFour()
    {
        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new MimeType('text', 'html', new QualityFactor(1))
            )
            ->addType(
                new MimeType('application', 'xml', new QualityFactor(1))
            );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser('text/html,bob, application/xml'));
    }


    public function testGetInvalidTypeOne()
    {
        $type = 'bob';

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            '"' . $type . '" is not a valid mime type'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->get($type, 0.5);
    }


    public function testGetInvalidTypeTwo()
    {
        $type = new \stdClass();

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid type provided to builder.'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->get($type, 0.5);
    }


    public function testParseAppInvalidWildcardTypeOne()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('*/*');
    }


    public function testParseAppInvalidWildcardTypeTwo()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('*/html');
    }


    public function testParseAppInvalidWildcardTypeThree()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('text/*');
    }
}
