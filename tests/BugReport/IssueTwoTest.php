<?php

/**
 * Test to verify the no regressions for Issue #2 occur.
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
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\TypePair\MimeTypePair;

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
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $expectCollection = new MimeTypePairCollection($sort);
        $expectCollection
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('text', 'turtle', new QualityFactor(1))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'rdf+json', new QualityFactor(0.8))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'rdf+xml', new QualityFactor(0.8))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('text', 'n3', new QualityFactor(0.8))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('text', 'rdf+n3', new QualityFactor(0.8))
                )
            )
            ->addPair(
                new MimeTypePair(
                    new AbsentMimeType(new QualityFactor(0)),
                    new MimeType('application', 'ld+json', new QualityFactor(0.5))
                )
            );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
