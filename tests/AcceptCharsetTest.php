<?php

/** Unit tests for Accept-Charset field.

    @version    AcceptCharsetTest.php v3.0-dev 2012-12-07
    @copyright  (c) 2012 ptlis
    @license    GNU Lesser General Public License v2.1
    @package    tests
    @author     Brian Ridley <ptlis@ptlis.net>
 */

namespace tests;


/** Tests of Accept-Charset negotiation. */
class AcceptCharsetTest extends \PHPUnit_Framework_TestCase
{

/** No Accept-Charset field sent, no server values were provided. */
    public function testAcceptCharsetEmptyAppEmpty() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = '';


        $bestType                           = false;
        $parsedTypes                        = false;


        $genBestType                        = \conNeg::charBest();
        $genParsedTypes                     = \conNeg::charAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Accept-Charset field sent, no server values were provided. */
    public function testAcceptCharsetSetAppEmpty() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = 'utf-8,iso-8859-5;q=0.75';


        $bestType                           = 'utf-8';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'utf-8';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'iso-8859-5';
        $parsedTypes['qFactorUser'][1]      = '0.75';


        $genBestType                        = \conNeg::charBest();
        $genParsedTypes                     = \conNeg::charAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Accept-Charset field not sent, server values were provided. */
    public function testAcceptCharsetEmptyAppSet() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = '';

        $appPref                            = 'utf-8;q=0.5,iso-8859-1;q=1';


        $bestType                           = false;

        $parsedTypes                        = false;


        $genBestType                        = \conNeg::charBest($appPref);
        $genParsedTypes                     = \conNeg::charAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Intersection between identical Accept-Charset field & server values with
    quality factors. */
    public function testIdenticalAcceptCharsetAndApp() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = 'utf-8;q=0.6,iso-8859-5;q=0.9';

        $appPref                            = 'iso-8859-5;q=0.9,utf-8;q=0.6';


        $bestType                           = 'iso-8859-5';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'iso-8859-5';
        $parsedTypes['qFactorApp'][0]       = '0.9';
        $parsedTypes['qFactorUser'][0]      = '0.9';
        $parsedTypes['qFactorProduct'][0]   = '0.81';

        $parsedTypes['type'][1]             = 'utf-8';
        $parsedTypes['qFactorApp'][1]       = '0.6';
        $parsedTypes['qFactorUser'][1]      = '0.6';
        $parsedTypes['qFactorProduct'][1]   = '0.36';


        $genBestType                        = \conNeg::charBest($appPref);
        $genParsedTypes                     = \conNeg::charAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }




/** Intersection between Accept-Charset field & server values with quality
    factors. */
    public function testAcceptCharsetIntersectionQFactors() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = 'utf-8;q=0.6,iso-8859-5;q=0.9,iso-8859-1;q=0.3';

        $appPref                            = 'windows-1250;q=0.8,utf-8;q=0.3,iso-8859-1;q=0.5';


        $bestType                           = 'utf-8';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'utf-8';
        $parsedTypes['qFactorApp'][0]       = '0.3';
        $parsedTypes['qFactorUser'][0]      = '0.6';
        $parsedTypes['qFactorProduct'][0]   = '0.18';

        $parsedTypes['type'][1]             = 'iso-8859-1';
        $parsedTypes['qFactorApp'][1]       = '0.5';
        $parsedTypes['qFactorUser'][1]      = '0.3';
        $parsedTypes['qFactorProduct'][1]   = '0.15';


        $genBestType                        = \conNeg::charBest($appPref);
        $genParsedTypes                     = \conNeg::charAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Intersection between Accept-Charset field & server values where the field
    contains a wildcard. */
    public function testAcceptCharsetWildcardIntersection() {
        $_SERVER['HTTP_ACCEPT_CHARSET']     = 'iso-8859-5;q=0.8,utf-8;q=0.9,*;q=0.5';

        $appPref                            = 'iso-8859-5,utf-8;q=0.7,windows-1250;q=0.3';


        $bestType                           = 'iso-8859-5';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'iso-8859-5';
        $parsedTypes['qFactorApp'][0]       = '1';
        $parsedTypes['qFactorUser'][0]      = '0.8';
        $parsedTypes['qFactorProduct'][0]   = '0.8';

        $parsedTypes['type'][1]             = 'utf-8';
        $parsedTypes['qFactorApp'][1]       = '0.7';
        $parsedTypes['qFactorUser'][1]      = '0.9';
        $parsedTypes['qFactorProduct'][1]   = '0.63';

        $parsedTypes['type'][2]             = 'windows-1250';
        $parsedTypes['qFactorApp'][2]       = '0.3';
        $parsedTypes['qFactorUser'][2]      = '0.5';
        $parsedTypes['qFactorProduct'][2]   = '0.15';


        $genBestType                        = \conNeg::charBest($appPref);
        $genParsedTypes                     = \conNeg::charAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }
}
