<?php

/**
 * Test to verify the use of the Negotiate::languageBest method.
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
use ptlis\ConNeg\Type\Shared\AbsentType;
use ptlis\ConNeg\Type\Language\LanguageType;
use ptlis\ConNeg\TypePair\SharedTypePair;

class LanguageBestTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $expectPair = new SharedTypePair(
            new AbsentType(new QualityFactor(0)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testAppEmpty()
    {
        $httpField  = 'en-us,fr;q=0.75';
        $appPrefs   = '';

        $expectPair = new SharedTypePair(
            new AbsentType(new QualityFactor(0)),
            new LanguageType('en-us', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'en-gb;q=1,en-us;q=0.5';

        $expectPair = new SharedTypePair(
            new LanguageType('en-gb', new QualityFactor(1)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'en-us;q=0.5,en-gb;q=0.5';

        $expectPair = new SharedTypePair(
            new LanguageType('en-gb', new QualityFactor(0.5)),
            new AbsentType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'en-us;q=0.6,fr;q=0.9';
        $appPrefs   = 'fr;q=0.9,en-us;q=0.6';

        $expectPair = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.9)),
            new LanguageType('fr', new QualityFactor(0.9))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'en-us;q=0.6,fr;q=0.9,en-gb;q=0.3';
        $appPrefs   = 'es;q=0.8,en-us;q=0.3,en-gb;q=0.5';

        $expectPair = new SharedTypePair(
            new LanguageType('en-us', new QualityFactor(0.3)),
            new LanguageType('en-us', new QualityFactor(0.6))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }


    public function testWildcard()
    {
        $httpField  = 'fr;q=0.8,en-us;q=0.9,*;q=0.5';
        $appPrefs   = 'fr,en-us;q=0.7,es;q=0.3';

        $expectPair = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(1)),
            new LanguageType('fr', new QualityFactor(0.8))
        );

        $negotiate = new Negotiate();
        $resultType = $negotiate->languageBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultType);
    }
}
