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

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\MimeAbsentType;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\TypePair\TypePair;

class IssueThreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link    https://github.com/ptlis/conneg/issues/3 GitHub Issue #3.
     */
    public function testOne()
    {
        $httpField = 'application/rdf+xml;q=0.5,text/html;q=.3';
        $appPrefs = '';

        $sort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectList = array(
            new TypePair(
                new MimeType('application', 'rdf+xml', new QualityFactor(0.5)),
                new MimeAbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(0.3)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new TypePairCollection($sort, $expectList);

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

        $sort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new MimeType('application', 'xhtml+xml', new QualityFactor(0.5)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($sort, $pairList);

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

        $sort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new MimeType('application', 'rdf+xml', new QualityFactor(0.5)),
                new MimeAbsentType(new QualityFactor(0))
            ),
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(0.5)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );
        $expectCollection = new TypePairCollection($sort, $pairList);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
