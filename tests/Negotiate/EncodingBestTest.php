<?php

/**
 * Test to verify the use of the Negotiate::encodingBest method.
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

use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

class EncodingBestTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $expectPair = new TypePair(
            new AbsentType(new QualityFactor(0)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testAppEmpty()
    {
        $httpField  = '7zip,gzip;q=0.75';
        $appPrefs   = '';

        $expectPair = new TypePair(
            new Type('7zip', new QualityFactor(1)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'compress;q=1,7zip;q=0.5';

        $expectPair = new TypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('compress', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = '7zip;q=0.5,compress;q=0.5';

        $expectPair = new TypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('7zip', new QualityFactor(0.5))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = '7zip;q=0.6,gzip;q=0.9';
        $appPrefs   = 'gzip;q=0.9,7zip;q=0.6';

        $expectPair = new TypePair(
            new Type('gzip', new QualityFactor(0.9)),
            new Type('gzip', new QualityFactor(0.9))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = '7zip;q=0.6,gzip;q=0.9,compress;q=0.3';
        $appPrefs   = 'deflate;q=0.8,7zip;q=0.3,compress;q=0.5';

        $expectPair = new TypePair(
            new Type('7zip', new QualityFactor(0.6)),
            new Type('7zip', new QualityFactor(0.3))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testWildcard()
    {
        $httpField  = 'gzip;q=0.8,7zip;q=0.9,*;q=0.5';
        $appPrefs   = 'gzip,7zip;q=0.7,deflate;q=0.3';

        $expectPair = new TypePair(
            new Type('gzip', new QualityFactor(0.8)),
            new Type('gzip', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->encodingBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }
}
