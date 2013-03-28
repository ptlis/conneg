<?php

/** Unit tests for Accept field.

    @version    AcceptTest.php v3.0-dev 2012-12-07
    @copyright  (c) 2012 ptlis
    @license    GNU Lesser General Public License v2.1
    @package    tests
    @author     Brian Ridley <ptlis@ptlis.net>
 */

namespace tests;


/** Tests of Accept negotiation. */
class AcceptTest extends \PHPUnit_Framework_TestCase
{

/******************************************************************************
 * Basic Accept negotiation, without accept-extensions fragments.             *
 ******************************************************************************/

/** No Accept field sent, no server values were provided. */
    public function testAcceptEmptyAppEmpty() {
        $_SERVER['HTTP_ACCEPT']             = '';

        $bestType                           = false;
        $parsedTypes                        = false;


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** No accept field sent, server values provided. */
    public function testAcceptEmptyAppSet() {
        $_SERVER['HTTP_ACCEPT']             = '';

        $appPref                            = 'text/html;q=1,application/xhtml+xml;q=0.8';


        $bestType                           = false;
        $parsedTypes                        = false;


        $genBestType                        = \conNeg::mimeBest($appPref);
        $genParsedTypes                     = \conNeg::mimeAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Accept field sent, no server values provided. */
    public function testAcceptSetAppEmpty() {
        $_SERVER['HTTP_ACCEPT']             = 'text/html;q=0.7,text/xml';

        $appPref                            = '';


        $bestType                           = 'text/xml';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'text/xml';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'text/html';
        $parsedTypes['qFactorUser'][1]      = '0.7';


        $genBestType                        = \conNeg::mimeBest($appPref);
        $genParsedTypes                     = \conNeg::mimeAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Intersection between identical Accept field & server values with quality
    factors. */
    public function testIdenticalAcceptAndApp() {
        $_SERVER['HTTP_ACCEPT']             = 'text/html;q=1,application/xhtml+xml;q=1';

        $appPref                            = 'application/xhtml+xml;q=1,text/html;q=1';


        $bestType                           = 'application/xhtml+xml';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'application/xhtml+xml';
        $parsedTypes['qFactorApp'][0]       = '1';
        $parsedTypes['qFactorUser'][0]      = '1';
        $parsedTypes['qFactorProduct'][0]   = '1';

        $parsedTypes['type'][1]             = 'text/html';
        $parsedTypes['qFactorApp'][1]       = '1';
        $parsedTypes['qFactorUser'][1]      = '1';
        $parsedTypes['qFactorProduct'][1]   = '1';


        $genBestType                        = \conNeg::mimeBest($appPref);
        $genParsedTypes                     = \conNeg::mimeAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Accept field is populated & application preferences provided. */
    public function testOverlappingAcceptAndApp() {
        $_SERVER['HTTP_ACCEPT']             = 'text/html;q=1,application/xml';

        $appPref                            = 'image/png;q=0.7,text/plain;q=0.3,text/html;q=0.4,application/xml';


        $bestType                           = 'application/xml';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'application/xml';
        $parsedTypes['qFactorApp'][0]       = '1';
        $parsedTypes['qFactorUser'][0]      = '1';
        $parsedTypes['qFactorProduct'][0]   = '1';

        $parsedTypes['type'][1]             = 'text/html';
        $parsedTypes['qFactorApp'][1]       = '0.4';
        $parsedTypes['qFactorUser'][1]      = '1';
        $parsedTypes['qFactorProduct'][1]   = '0.4';


        $genBestType                        = \conNeg::mimeBest($appPref);
        $genParsedTypes                     = \conNeg::mimeAll($appPref);

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }
}
