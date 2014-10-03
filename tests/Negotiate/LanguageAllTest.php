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

use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\LanguageType;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\SharedTypePair;

class LanguageAllTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmpty()
    {
        $httpField  = 'en-us,fr;q=0.75';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-us', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('fr', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-us', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-gb', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('de', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('en-gb', new QualityFactor(0.5))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('en-gb', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('fr', new QualityFactor(0.5))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('en-us', new QualityFactor(0.5))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('fr', new QualityFactor(0.5))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-us', new QualityFactor(0.9)),
                new LanguageType('en-us', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('fr', new QualityFactor(0.6)),
                new LanguageType('fr', new QualityFactor(0.3))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-gb', new QualityFactor(0.3)),
                new LanguageType('en-gb', new QualityFactor(0.5))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-us', new QualityFactor(0.9)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new LanguageType('es', new QualityFactor(0.8))
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

        $pairSort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('en-gb', new QualityFactor(0.8)),
                new LanguageType('en-gb', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new LanguageType('de', new QualityFactor(0.9)),
                new LanguageType('de', new QualityFactor(0.7))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new WildcardType(new QualityFactor(0.5)),
                new LanguageType('fr', new QualityFactor(0.3))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
