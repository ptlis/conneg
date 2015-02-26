<?php

/**
 * Test to verify the use of the Negotiate::charsetBest method.
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

namespace ptlis\ConNeg\Test\Negotiate;

use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

class CharsetBestTest extends \PHPUnit_Framework_TestCase
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
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testAppEmpty()
    {
        $httpField  = 'utf-8,iso-8859-5;q=0.75';
        $appPrefs   = '';

        $expectPair = new TypePair(
            new Type('utf-8', new QualityFactor(1)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'iso-8859-1;q=1,utf-8;q=0.5';

        $expectPair = new TypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('iso-8859-1', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'utf-8;q=0.5,iso-8859-1;q=0.5';

        $expectPair = new TypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('iso-8859-1', new QualityFactor(0.5))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'utf-8;q=0.6,iso-8859-5;q=0.9';
        $appPrefs   = 'iso-8859-5;q=0.9,utf-8;q=0.6';

        $expectPair = new TypePair(
            new Type('iso-8859-5', new QualityFactor(0.9)),
            new Type('iso-8859-5', new QualityFactor(0.9))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'utf-8;q=0.6,iso-8859-5;q=0.9,iso-8859-1;q=0.3';
        $appPrefs   = 'windows-1250;q=0.8,utf-8;q=0.3,iso-8859-1;q=0.5';

        $expectPair = new TypePair(
            new Type('utf-8', new QualityFactor(0.6)),
            new Type('utf-8', new QualityFactor(0.3))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testWildcard()
    {
        $httpField  = 'iso-8859-5;q=0.8,utf-8;q=0.9,*;q=0.5';
        $appPrefs   = 'iso-8859-5,utf-8;q=0.7,windows-1250;q=0.3';

        $expectPair = new TypePair(
            new Type('iso-8859-5', new QualityFactor(0.8)),
            new Type('iso-8859-5', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->charsetBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }
}
