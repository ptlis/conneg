<?php

/**
 * Test to verify the use of the Negotiate::languageAll method.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

class LanguageAllTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new TypePairCollection($pairSort, array());

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmpty()
    {
        $httpField  = 'en-us,fr;q=0.75';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('en-us', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new Type('fr', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmptyOutOfOrder()
    {
        $httpField  = 'en-gb;q=0.75,en-us';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('en-us', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new Type('en-gb', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'de;q=1,en-gb;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('de', new QualityFactor(1))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('en-gb', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyOutOfOrder()
    {
        $httpField  = '';
        $appPrefs   = 'fr;q=0.5,en-gb;q=1';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('en-gb', new QualityFactor(1))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('fr', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'fr;q=0.5,en-us;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('en-us', new QualityFactor(0.5))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('fr', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'de;q=0.6,en-us;q=0.9';
        $appPrefs   = 'en-us;q=0.9,de;q=0.6';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('en-us', new QualityFactor(0.9)),
                new Type('en-us', new QualityFactor(0.9))
            ),
            new TypePair(
                new Type('de', new QualityFactor(0.6)),
                new Type('de', new QualityFactor(0.6))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'fr;q=0.6,en-us;q=0.9,en-gb;q=0.3';
        $appPrefs   = 'es;q=0.8,fr;q=0.3,en-gb;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('fr', new QualityFactor(0.6)),
                new Type('fr', new QualityFactor(0.3))
            ),
            new TypePair(
                new Type('en-gb', new QualityFactor(0.3)),
                new Type('en-gb', new QualityFactor(0.5))
            ),
            new TypePair(
                new Type('en-us', new QualityFactor(0.9)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('es', new QualityFactor(0.8))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testWildcard()
    {
        $httpField  = 'en-gb;q=0.8,de;q=0.9,*;q=0.5';
        $appPrefs   = 'en-gb,de;q=0.7,fr;q=0.3';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('en-gb', new QualityFactor(0.8)),
                new Type('en-gb', new QualityFactor(1))
            ),
            new TypePair(
                new Type('de', new QualityFactor(0.9)),
                new Type('de', new QualityFactor(0.7))
            ),
            new TypePair(
                new WildcardType(new QualityFactor(0.5)),
                new Type('fr', new QualityFactor(0.3))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->languageAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}