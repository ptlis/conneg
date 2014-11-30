<?php

/**
 * Test to verify the use of the Negotiate::encodingAll method.
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
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

class EncodingAllTest extends \PHPUnit_Framework_TestCase
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

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmpty()
    {
        $httpField  = 'deflate,7zip;q=0.75';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('deflate', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new Type('7zip', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmptyOutOfOrder()
    {
        $httpField  = 'gzip;q=0.75,deflate';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('deflate', new QualityFactor(1)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new Type('gzip', new QualityFactor(0.75)),
                new AbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'compress;q=1,gzip;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('compress', new QualityFactor(1))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('gzip', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyOutOfOrder()
    {
        $httpField  = '';
        $appPrefs   = '7zip;q=0.5,gzip;q=1';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('gzip', new QualityFactor(1))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('7zip', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = '7zip;q=0.5,deflate;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('7zip', new QualityFactor(0.5))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('deflate', new QualityFactor(0.5))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'compress;q=0.6,deflate;q=0.9';
        $appPrefs   = 'deflate;q=0.9,compress;q=0.6';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('deflate', new QualityFactor(0.9)),
                new Type('deflate', new QualityFactor(0.9))
            ),
            new TypePair(
                new Type('compress', new QualityFactor(0.6)),
                new Type('compress', new QualityFactor(0.6))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = '7zip;q=0.6,deflate;q=0.9,gzip;q=0.3';
        $appPrefs   = 'bz;q=0.8,7zip;q=0.3,gzip;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('7zip', new QualityFactor(0.6)),
                new Type('7zip', new QualityFactor(0.3))
            ),
            new TypePair(
                new Type('gzip', new QualityFactor(0.3)),
                new Type('gzip', new QualityFactor(0.5))
            ),
            new TypePair(
                new Type('deflate', new QualityFactor(0.9)),
                new AbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new Type('bz', new QualityFactor(0.8))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testWildcard()
    {
        $httpField  = 'gzip;q=0.8,compress;q=0.9,*;q=0.5';
        $appPrefs   = 'gzip,compress;q=0.7,7zip;q=0.3';

        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('gzip', new QualityFactor(0.8)),
                new Type('gzip', new QualityFactor(1))
            ),
            new TypePair(
                new Type('compress', new QualityFactor(0.9)),
                new Type('compress', new QualityFactor(0.7))
            ),
            new TypePair(
                new WildcardType(new QualityFactor(0.5)),
                new Type('7zip', new QualityFactor(0.3))
            )
        );
        $expectCollection = new TypePairCollection($pairSort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->encodingAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
