<?php

/**
 * Test to verify the correctness of SharedTypeFactory with charsets.
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
use ptlis\ConNeg\Type\CharsetType;
use ptlis\ConNeg\TypeBuilder\CharsetTypeBuilder;
use ptlis\ConNeg\TypeFactory\SharedTypeFactory;
use ptlis\ConNeg\RegexProvider\SharedTypeRegexProvider;
use ptlis\ConNeg\Type\WildcardType;

class CharsetTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'utf-8,';
        $qFactor = 1;

        $expectType = new CharsetType($type, new QualityFactor($qFactor));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'utf-8;q=0.9';

        $expectType = new CharsetType('utf-8', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'utf-8';

        $expectType = new CharsetType('utf-8', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
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
            new CharsetTypeBuilder(new QualityFactorFactory())
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
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'utf-8;q=0.5;iso-8859-5';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new CharsetType('utf-8', new QualityFactor(0.5)));
        $expectCollection->addType(new CharsetType('iso-8859-5', new QualityFactor(1)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'utf-8;q=0.5;iso-8859-5;*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new CharsetType('utf-8', new QualityFactor(0.5)));
        $expectCollection->addType(new CharsetType('iso-8859-5', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExample()
    {
        $field = 'iso-8859-5, unicode-1-1;q=0.8';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new CharsetType('iso-8859-5', new QualityFactor(1)));
        $expectCollection->addType(new CharsetType('unicode-1-1', new QualityFactor(0.8)));

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
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
            new CharsetTypeBuilder(new QualityFactorFactory())
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
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $factory->get($type, 0.5);
    }


    public function testParseUserInvalidType()
    {
        $expectCollection = new TypeCollection();

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
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
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('*');
    }
}
