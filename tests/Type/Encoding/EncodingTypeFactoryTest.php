<?php

/**
 * Test to verify the correctness of SharedTypeFactory for encodings.
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
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;
use ptlis\ConNeg\TypeFactory\SharedTypeFactory;
use ptlis\ConNeg\RegexProvider\SharedTypeRegexProvider;
use ptlis\ConNeg\Type\WildcardType;

class TypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'gzip,';
        $qFactor = 1;

        $expectType = new Type($type, new QualityFactor($qFactor));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'gzip;q=0.9';

        $expectType = new Type('gzip', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'compress';

        $expectType = new Type('compress', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeIncludeQualityFactor()
    {
        $field = '*;q=0.5';

        $expectType = new WildcardType(new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeOmitQualityFactor()
    {
        $field = '*';

        $expectType = new WildcardType(new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'gzip,compress;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('gzip', new QualityFactor(1)));
        $expectCollection->addType(new Type('compress', new QualityFactor(0.7)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'compress;q=0.5,gzip,*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('compress', new QualityFactor(0.5)));
        $expectCollection->addType(new Type('gzip', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleOne()
    {
        $field = 'compress, gzip';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('compress', new QualityFactor(1)));
        $expectCollection->addType(new Type('gzip', new QualityFactor(1)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleTwo()
    {
        $field = 'compress;q=0.5, gzip;q=1.0';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('compress', new QualityFactor(0.5)));
        $expectCollection->addType(new Type('gzip', new QualityFactor(1)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleThree()
    {
        $field = 'gzip;q=1.0, identity; q=0.5, *;q=0';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('gzip', new QualityFactor(1)));
        $expectCollection->addType(new Type('identity', new QualityFactor(0.5)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseAppInvalidTypeOne()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Error parsing field'
        );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('$^(£$');
    }


    public function testGetInvalidTypeTwo()
    {
        $type = new \stdClass();

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid type provided to builder.'
        );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $factory->get($type, 0.5);
    }


    public function testParseUserInvalidType()
    {
        $expectCollection = new TypeCollection();

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser('$^(£$'));
    }


    public function testParseAppInvalidWildcardType()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('*');
    }
}
