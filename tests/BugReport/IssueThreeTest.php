<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\BugReport;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\TypePair\TypePair;

/**
 * Regression tests for Issue #3
 */
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
                new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        $expectList = array(
            new TypePair(
                new MimeType('application', 'rdf+xml', 0.5, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('text', 'html', 0.3, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        $expectCollection = new TypePairCollection($sort, $expectList);

        $negotiate = new Negotiation();
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
                new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new TypePair(
                new MimeType('application', 'xhtml+xml', 0.5, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );
        $expectCollection = new TypePairCollection($sort, $pairList);

        $negotiate = new Negotiation();
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
                new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new TypePair(
                new MimeType('application', 'rdf+xml', 0.5, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('text', 'html', 0.5, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );
        $expectCollection = new TypePairCollection($sort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
