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
 * Regression tests for Issue #2.
 */
class IssueTwoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @link https://github.com/ptlis/conneg/issues/2 GitHub Issue #2.
     */
    public function testOne()
    {
        $httpField = 'text/rdf+n3; q=0.8, application/rdf+json; q=0.8, text/turtle; q=1.0, text/n3; q=0.8, application/ld+json; q=0.5, application/rdf+xml; q=0.8';
        $appPrefs = '';

        $sort = new TypePairSort(
            new TypePair(
                new MimeType('', '', 0, MimeType::ABSENT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new TypePair(
                new MimeType('text', 'turtle', 1, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('application', 'rdf+json', 0.8, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('application', 'rdf+xml', 0.8, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('text', 'n3', 0.8, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('text', 'rdf+n3', 0.8, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            ),
            new TypePair(
                new MimeType('application', 'ld+json', 0.5, MimeType::EXACT_TYPE),
                new MimeType('', '', 0, MimeType::ABSENT_TYPE)
            )
        );
        $expectCollection = new TypePairCollection($sort, $pairList);

        $negotiate = new Negotiation();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
