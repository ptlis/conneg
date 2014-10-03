<?php

/**
 * Test to verify the correctness of SharedTypeFactory with languages.
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

namespace ptlis\ConNeg\Test\Type\Language;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;
use ptlis\ConNeg\TypeFactory\TypeFactory;
use ptlis\ConNeg\RegexProvider\TypeRegexProvider;
use ptlis\ConNeg\Type\WildcardType;

class LanguageTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'en-gb,';
        $qFactor = 1;

        $expectType = new Type($type, new QualityFactor($qFactor));

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'fr;q=0.9';

        $expectType = new Type('fr', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'en-us';

        $expectType = new Type('en-us', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new TypeFactory(
            new TypeRegexProvider(),
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

        $factory = new TypeFactory(
            new TypeRegexProvider(),
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

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'en-gb,en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('en-gb', new QualityFactor(1)));
        $expectCollection->addType(new Type('en', new QualityFactor(0.7)));

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'fr;q=0.5,de,*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('fr', new QualityFactor(0.5)));
        $expectCollection->addType(new Type('de', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExample()
    {
        $field = 'da, en-gb;q=0.8, en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new Type('da', new QualityFactor(1)));
        $expectCollection->addType(new Type('en-gb', new QualityFactor(0.8)));
        $expectCollection->addType(new Type('en', new QualityFactor(0.7)));

        $factory = new TypeFactory(
            new TypeRegexProvider(),
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

        $factory = new TypeFactory(
            new TypeRegexProvider(),
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

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $factory->get($type, 0.5);
    }


    public function testParseUserInvalidType()
    {
        $expectCollection = new TypeCollection();

        $factory = new TypeFactory(
            new TypeRegexProvider(),
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

        $factory = new TypeFactory(
            new TypeRegexProvider(),
            new TypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp('*');
    }
}
