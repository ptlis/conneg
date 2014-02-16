<?php

/**
 * Test to verify the use of the Negotiate::languageAll method.
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

namespace ptlis\ConNeg\Test\Negotiate;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Language\LanguageType;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

class languageAllTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $expectCollection = new TypePairCollection();

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmpty()
    {
        $httpField  = 'en-us,fr;q=0.75';
        $appPrefs   = '';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new AbsentType(),
                new LanguageType('en-us', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new AbsentType(),
                new LanguageType('fr', new QualityFactor(0.75))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmptyOutOfOrder()
    {
        $httpField  = 'en-gb;q=0.75,en-us';
        $appPrefs   = '';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new AbsentType(),
                new LanguageType('en-us', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new AbsentType(),
                new LanguageType('en-gb', new QualityFactor(0.75))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'de;q=1,en-gb;q=0.5';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('de', new QualityFactor(1)),
                new AbsentType()
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-gb', new QualityFactor(0.5)),
                new AbsentType()
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyOutOfOrder()
    {
        $httpField  = '';
        $appPrefs   = 'fr;q=0.5,en-gb;q=1';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-gb', new QualityFactor(1)),
                new AbsentType()
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('fr', new QualityFactor(0.5)),
                new AbsentType()
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'fr;q=0.5,en-us;q=0.5';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-us', new QualityFactor(0.5)),
                new AbsentType()
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('fr', new QualityFactor(0.5)),
                new AbsentType()
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'de;q=0.6,en-us;q=0.9';
        $appPrefs   = 'en-us;q=0.9,de;q=0.6';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-us', new QualityFactor(0.9)),
                new LanguageType('en-us', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('de', new QualityFactor(0.6)),
                new LanguageType('de', new QualityFactor(0.6))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'fr;q=0.6,en-us;q=0.9,en-gb;q=0.3';
        $appPrefs   = 'es;q=0.8,fr;q=0.3,en-gb;q=0.5';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('fr', new QualityFactor(0.3)),
                new LanguageType('fr', new QualityFactor(0.6))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-gb', new QualityFactor(0.5)),
                new LanguageType('en-gb', new QualityFactor(0.3))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new AbsentType(),
                new LanguageType('en-us', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('es', new QualityFactor(0.8)),
                new AbsentType()
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testWildcard()
    {
        $httpField  = 'en-gb;q=0.8,de;q=0.9,*;q=0.5';
        $appPrefs   = 'en-gb,de;q=0.7,fr;q=0.3';

        $expectCollection = new TypePairCollection();
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('en-gb', new QualityFactor(1)),
                new LanguageType('en-gb', new QualityFactor(0.8))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('de', new QualityFactor(0.7)),
                new LanguageType('de', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new LanguageType('fr', new QualityFactor(0.3)),
                new WildcardType(new QualityFactor(0.5))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
