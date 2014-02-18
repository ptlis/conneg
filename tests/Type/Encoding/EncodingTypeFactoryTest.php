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
use ptlis\ConNeg\RegexProvider;
use ptlis\ConNeg\Type\Encoding\EncodingType;
use ptlis\ConNeg\Type\SharedTypeFactory;
use ptlis\ConNeg\Type\WildcardType;

class EncodingTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'gzip,';
        $qFactor = 1;

        $expectType = new EncodingType($type, new QualityFactor($qFactor));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'gzip;q=0.9';

        $expectType = new EncodingType('gzip', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'compress';

        $expectType = new EncodingType('compress', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeIncludeQualityFactor()
    {
        $field = '*;q=0.5';

        $expectType = new WildcardType(new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleWildcardTypeOmitQualityFactor()
    {
        $field = '*';

        $expectType = new WildcardType(new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'gzip,compress;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new EncodingType('gzip', new QualityFactor(1)));
        $expectCollection->addType(new EncodingType('compress', new QualityFactor(0.7)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'compress;q=0.5,gzip,*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new EncodingType('compress', new QualityFactor(0.5)));
        $expectCollection->addType(new EncodingType('gzip', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleOne()
    {
        $field = 'compress, gzip';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new EncodingType('compress', new QualityFactor(1)));
        $expectCollection->addType(new EncodingType('gzip', new QualityFactor(1)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleTwo()
    {
        $field = 'compress;q=0.5, gzip;q=1.0';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new EncodingType('compress', new QualityFactor(0.5)));
        $expectCollection->addType(new EncodingType('gzip', new QualityFactor(1)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExampleThree()
    {
        $field = 'gzip;q=1.0, identity; q=0.5, *;q=0';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new EncodingType('gzip', new QualityFactor(1)));
        $expectCollection->addType(new EncodingType('identity', new QualityFactor(0.5)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getEncodingRegex(),
            'ptlis\ConNeg\Type\Encoding\EncodingType'
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }
}
