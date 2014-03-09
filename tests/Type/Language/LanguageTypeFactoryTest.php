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

namespace ptlis\ConNeg\Test\Type\Charset;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\RegexProvider;
use ptlis\ConNeg\Type\Language\LanguageType;
use ptlis\ConNeg\Type\SharedTypeFactory;
use ptlis\ConNeg\Type\WildcardType;

class LanguageTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'en-gb,';
        $qFactor = 1;

        $expectType = new LanguageType($type, new QualityFactor($qFactor));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'fr;q=0.9';

        $expectType = new LanguageType('fr', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'en-us';

        $expectType = new LanguageType('en-us', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
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
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
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
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'en-gb,en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('en-gb', new QualityFactor(1)));
        $expectCollection->addType(new LanguageType('en', new QualityFactor(0.7)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'fr;q=0.5,de,*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('fr', new QualityFactor(0.5)));
        $expectCollection->addType(new LanguageType('de', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testParseSpecExample()
    {
        $field = 'da, en-gb;q=0.8, en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('da', new QualityFactor(1)));
        $expectCollection->addType(new LanguageType('en-gb', new QualityFactor(0.8)));
        $expectCollection->addType(new LanguageType('en', new QualityFactor(0.7)));

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testInvalidTypeClass()
    {
        $typeClass = 'ptlis\ConNeg\Type\bob\bob';

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            '"' . $typeClass . '" does not implement TypeInterface'
        );

        $regexProvider = new RegexProvider();
        new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            $typeClass,
            new QualityFactorFactory()
        );
    }


    public function testParseAppInvalidType()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Error parsing field'
        );

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $factory->parseApp('$^(£$');
    }


    public function testParseUserInvalidType()
    {
        $expectCollection = new TypeCollection();

        $regexProvider = new RegexProvider();
        $factory = new SharedTypeFactory(
            $regexProvider->getLanguageRegex(),
            'ptlis\ConNeg\Type\Language\LanguageType',
            new QualityFactorFactory()
        );

        $this->assertEquals($expectCollection, $factory->parseUser('$^(£$'));
    }
}
