<?php

/** Unit tests based one examples taken from RFC2616

    @version    SpecExamplesTest.php v3.0-dev 2012-12-07
    @copyright  (c) 2012 ptlis
    @package    tests
    @license    GNU Lesser General Public License v2.1
    @author     Brian Ridley <ptlis@ptlis.net>
 */

namespace tests;


/** Tests of negotiation on field from RFC2616. */
class SpecExamplesTest extends \PHPUnit_Framework_TestCase
{

    /** First Accept test taken from RFC2616 Section 14.1 */
    public function testAcceptOne()
    {
        $_SERVER['HTTP_ACCEPT']             = 'audio/*; q=0.2, audio/basic';


        $bestType                           = 'audio/basic';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'audio/basic';
        $parsedTypes['qFactorUser'][0]      = '1';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Second Accept test taken from RFC2616 Section 14.1 */
    public function testAcceptTwo()
    {
        $_SERVER['HTTP_ACCEPT']             = 'text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c';


        $bestType                          = 'text/html';
        $parsedTypes                       = array();

        $parsedTypes['type'][0]            = 'text/html';
        $parsedTypes['qFactorUser'][0]     = '1';

        $parsedTypes['type'][1]            = 'text/x-c';
        $parsedTypes['qFactorUser'][1]     = '1';

        $parsedTypes['type'][2]            = 'text/x-dvi';
        $parsedTypes['qFactorUser'][2]     = '0.8';

        $parsedTypes['type'][3]            = 'text/plain';
        $parsedTypes['qFactorUser'][3]     = '0.5';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Third Accept test taken from RFC2616 Section 14.1 */
    public function testAcceptThree()
    {
        $_SERVER['HTTP_ACCEPT']             = 'text/*, text/html, text/html;level=1, */*';

        $bestType                           = 'text/html';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'text/html';
        $parsedTypes['qFactorUser'][0]      = '1';
        $parsedTypes['level'][0]            = null;


        $parsedTypes['type'][1]             = 'text/html';
        $parsedTypes['qFactorUser'][1]      = '1';
        $parsedTypes['level'][1]            = '1';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Accept-Charset test taken from RFC2616 Section 14.2 */
    public function testAcceptCharset() {
        $_SERVER['HTTP_ACCEPT_CHARSET']    = 'iso-8859-5, unicode-1-1;q=0.8';

        $error                             = false;
        $bestType                          = 'iso-8859-5';
        $parsedTypes                       = array();

        $parsedTypes['type'][0]            = 'iso-8859-5';
        $parsedTypes['qFactorUser'][0]     = '1';

        $parsedTypes['type'][1]            = 'unicode-1-1';
        $parsedTypes['qFactorUser'][1]     = '0.8';


        $genBestType                        = \conNeg::charBest();
        $genParsedTypes                     = \conNeg::charAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** First Accept-Encoding test taken from RFC2616 Section 14.3 */
    public function testAcceptEncodingOne() {
        $_SERVER['HTTP_ACCEPT_ENCODING']    = 'compress, gzip';

        $error                              = false;
        $bestType                           = 'compress';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'compress';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'gzip';
        $parsedTypes['qFactorUser'][1]      = '1';


        $genBestType                        = \conNeg::encBest();
        $genParsedTypes                     = \conNeg::encAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Second Accept-Encoding test taken from RFC2616 Section 14.3 */
    public function testAcceptEncodingTwo() {
        $_SERVER['HTTP_ACCEPT_ENCODING']    = 'compress;q=0.5, gzip;q=1.0';

        $error                              = false;
        $bestType                           = 'gzip';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'gzip';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'compress';
        $parsedTypes['qFactorUser'][1]      = '0.5';


        $genBestType                        = \conNeg::encBest();
        $genParsedTypes                     = \conNeg::encAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Fourth Accept-Encoding test taken from RFC2616 Section 14.3 */
    public function testAcceptEncodingThree() {
        $_SERVER['HTTP_ACCEPT_ENCODING']    = 'gzip;q=1.0, identity; q=0.5, *;q=0';

        $error                              = false;
        $bestType                           = 'gzip';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'gzip';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'identity';
        $parsedTypes['qFactorUser'][1]      = '0.5';


        $genBestType                        = \conNeg::encBest();
        $genParsedTypes                     = \conNeg::encAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


    /** Accept-Language test taken from RFC2616 Section 14.4 */
    public function testAcceptLanguage() {
        $_SERVER['HTTP_ACCEPT_LANGUAGE']    = 'da, en-gb;q=0.8, en;q=0.7';


        $bestType                           = 'da';
        $parsedTypes                        = array();

        $parsedTypes['type'][0]             = 'da';
        $parsedTypes['qFactorUser'][0]      = '1';

        $parsedTypes['type'][1]             = 'en-gb';
        $parsedTypes['qFactorUser'][1]      = '0.8';

        $parsedTypes['type'][2]             = 'en';
        $parsedTypes['qFactorUser'][2]      = '0.7';


        $genBestType                        = \conNeg::langBest();
        $genParsedTypes                     = \conNeg::langAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }
}
