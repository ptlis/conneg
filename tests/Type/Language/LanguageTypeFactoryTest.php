<?php

/**
 * Test to verify the correctness of LanguageTypeFactory.
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
use ptlis\ConNeg\Type\Language\LanguageType;
use ptlis\ConNeg\Type\Language\LanguageTypeFactory;
use ptlis\ConNeg\Type\WildcardType;

class LanguageTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $type = 'en-gb,';
        $qFactor = 1;

        $expectType = new LanguageType($type, new QualityFactor($qFactor));

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectType, $factory->get($type, $qFactor));
    }


    public function testParseEmpty()
    {
        $field = '';

        $expectCollection = new TypeCollection();

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleTypeIncludeQualityFactor()
    {
        $field = 'fr;q=0.9';

        $expectType = new LanguageType('fr', new QualityFactor(0.9));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleTypeOmitQualityFactor()
    {
        $field = 'en-us';

        $expectType = new LanguageType('en-us', new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardTypeIncludeQualityFactor()
    {
        $field = '*';

        $expectType = new WildcardType(new QualityFactor(1));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSingleWildcardTypeOmitQualityFactor()
    {
        $field = '*;q=0.5';

        $expectType = new WildcardType(new QualityFactor(0.5));
        $expectCollection = new TypeCollection();
        $expectCollection->addType($expectType);

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseMultipleOne()
    {
        $field = 'en-gb,en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('en-gb', new QualityFactor(1)));
        $expectCollection->addType(new LanguageType('en', new QualityFactor(0.7)));

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseMultipleTwo()
    {
        $field = 'fr;q=0.5,de,*;q=0.35';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('fr', new QualityFactor(0.5)));
        $expectCollection->addType(new LanguageType('de', new QualityFactor(1)));
        $expectCollection->addType(new WildcardType(new QualityFactor(0.35)));

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }


    public function testParseSpecExample()
    {
        $field = 'da, en-gb;q=0.8, en;q=0.7';

        $expectCollection = new TypeCollection();
        $expectCollection->addType(new LanguageType('da', new QualityFactor(1)));
        $expectCollection->addType(new LanguageType('en-gb', new QualityFactor(0.8)));
        $expectCollection->addType(new LanguageType('en', new QualityFactor(0.7)));

        $factory = new LanguageTypeFactory(new RegexProvider());

        $this->assertEquals($expectCollection, $factory->parse($field));
    }
}