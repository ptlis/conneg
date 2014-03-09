<?php

/**
 * Test to verify the no regressions for Issue #3 occur.
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

namespace ptlis\ConNeg\Test\BugReport;

use ptlis\ConNeg\Collection\MimeTypePairCollection;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\TypePair\MimeTypePair;

class IssueThreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testOne()
    {
        $httpField = 'application/rdf+xml;q=0.5,text/html;q=.3';
        $appPrefs = '';

        $expectCollection = new MimeTypePairCollection();
        $expectCollection
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'rdf+xml', new QualityFactor(0.5))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('text', 'html', new QualityFactor(0.3))
                )
            );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testTwo()
    {
        $httpField = 'application/xhtml+xml;q=0.5';
        $appPrefs = '';

        $expectCollection = new MimeTypePairCollection();
        $expectCollection
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'xhtml+xml', new QualityFactor(0.5))
                )
            );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testThree()
    {
        $httpField = 'application/rdf+xml;q=0.5,text/html;q=.5';
        $appPrefs = '';

        $expectCollection = new MimeTypePairCollection();
        $expectCollection
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'rdf+xml', new QualityFactor(0.5))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('text', 'html', new QualityFactor(0.5))
                )
            );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
