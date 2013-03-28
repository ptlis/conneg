<?php

/** Unit tests for Accept field.

    @version    BugReportTest.php v3.0-dev 2013-03-09
    @copyright  (c) 2013 ptlis
    @license    GNU Lesser General Public License v2.1
    @package    tests
    @author     Brian Ridley <ptlis@ptlis.net>
 */

namespace tests;


/** Unit tests written against bug reports. */
class BugReportTest extends \PHPUnit_Framework_TestCase
{


/** Testcase for Accept header written against Issue #2 on GitHub.

    @link https://github.com/ptlis/conneg/issues/2 GitHub Issue #2. */
    public function testGithubIssue2() {
        $_SERVER['HTTP_ACCEPT']         = 'text/rdf+n3; q=0.8, application/rdf+json; q=0.8, text/turtle; q=1.0, text/n3; q=0.8, application/ld+json; q=0.5, application/rdf+xml; q=0.8';


        $bestType                       = 'text/turtle';
        $parsedTypes                    = array();

        $parsedTypes['type'][0]         = 'text/turtle';
        $parsedTypes['qFactorUser'][0]  = '1';

        $parsedTypes['type'][1]         = 'application/rdf+json';
        $parsedTypes['qFactorUser'][1]  = '0.8';

        $parsedTypes['type'][2]         = 'application/rdf+xml';
        $parsedTypes['qFactorUser'][2]  = '0.8';

        $parsedTypes['type'][3]         = 'text/n3';
        $parsedTypes['qFactorUser'][3]  = '0.8';

        $parsedTypes['type'][4]         = 'text/rdf+n3';
        $parsedTypes['qFactorUser'][4]  = '0.8';

        $parsedTypes['type'][5]         = 'application/ld+json';
        $parsedTypes['qFactorUser'][5]  = '0.5';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Testcase for Accept header written against the first Linked Data Vapour
    Failure in Issue #3 on GitHub.

    @link https://github.com/ptlis/conneg/issues/3 GitHub Issue #3. */
    public function testGithubIssue3VapourFailiure1() {
        $_SERVER['HTTP_ACCEPT']         = 'application/rdf+xml;q=0.5,text/html;q=.3';

        $bestType                       = 'application/rdf+xml';
        $parsedTypes                    = array();

        $parsedTypes['type'][0]         = 'application/rdf+xml';
        $parsedTypes['qFactorUser'][0]  = '0.5';

        $parsedTypes['type'][1]         = 'text/html';
        $parsedTypes['qFactorUser'][1]  = '0.3';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Testcase for Accept header written against the second Linked Data Vapour
    Failure in Issue #3 on GitHub.

    @link https://github.com/ptlis/conneg/issues/3 GitHub Issue #3. */
    public function testGithubIssue3VapourFailiure2() {
        $_SERVER['HTTP_ACCEPT']         = 'application/xhtml+xml;q=0.5';


        $bestType                       = 'application/xhtml+xml';
        $parsedTypes                    = array();

        $parsedTypes['type'][0]         = 'application/xhtml+xml';
        $parsedTypes['qFactorUser'][0]  = '0.5';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


/** Testcase for Accept header written against the third Linked Data Vapour
    Failure in Issue #3 on GitHub.

    @link https://github.com/ptlis/conneg/issues/3 GitHub Issue #3. */
    public function testGithubIssue3VapourFailiure3() {
        $_SERVER['HTTP_ACCEPT']         = 'application/rdf+xml;q=0.5,text/html;q=.5';


        $bestType                       = 'application/rdf+xml';
        $parsedTypes                    = array();

        $parsedTypes['type'][0]         = 'application/rdf+xml';
        $parsedTypes['qFactorUser'][0]  = '0.5';

        $parsedTypes['type'][1]         = 'text/html';
        $parsedTypes['qFactorUser'][1]  = '0.5';


        $genBestType                        = \conNeg::mimeBest();
        $genParsedTypes                     = \conNeg::mimeAll();

        $this->assertSame($bestType, $genBestType);
        $this->assertSame($parsedTypes, $genParsedTypes);
    }


}