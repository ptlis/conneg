<?php

/**
 * Test to verify the use of the Negotiate::mimeAll method.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source coapplication/xml.
 */

namespace ptlis\ConNeg\Test\Negotiate;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\MimeAbsentType;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\MimeWildcardSubType;
use ptlis\ConNeg\Type\MimeWildcardType;
use ptlis\ConNeg\TypePair\TypePair;

class MimeAllTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAndAppEmptyCollection()
    {
        $httpField  = '';
        $appPrefs   = new TypeCollection();

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmpty()
    {
        $httpField  = 'text/html,application/xml;q=0.75';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'xml', new QualityFactor(0.75)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testAppEmptyOutOfOrder()
    {
        $httpField  = 'application/xml;q=0.75, text/html';
        $appPrefs   = '';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'xml', new QualityFactor(0.75)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'application/rdf+xml;q=1,text/n3;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('application', 'rdf+xml', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('text', 'n3', new QualityFactor(0.5))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyOutOfOrder()
    {
        $httpField  = '';
        $appPrefs   = 'text/n3;q=0.5,application/rdf+xml;q=1';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('application', 'rdf+xml', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('text', 'n3', new QualityFactor(0.5))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'text/n3;q=0.5,text/html;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('text', 'html', new QualityFactor(0.5))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('text', 'n3', new QualityFactor(0.5))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'application/rdf+xml;q=0.6,text/n3;q=0.9';
        $appPrefs   = 'text/n3;q=0.9,application/rdf+xml;q=0.6';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'n3', new QualityFactor(0.9)),
                new MimeType('text', 'n3', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'rdf+xml', new QualityFactor(0.6)),
                new MimeType('application', 'rdf+xml', new QualityFactor(0.6))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'text/html;q=0.6,application/xml;q=0.9,application/rss+xml;q=0.3';
        $appPrefs   = 'application/atom+xml;q=0.8,text/html;q=0.3,application/rss+xml;q=0.5';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(0.6)),
                new MimeType('text', 'html', new QualityFactor(0.3))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'rss+xml', new QualityFactor(0.3)),
                new MimeType('application', 'rss+xml', new QualityFactor(0.5))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'xml', new QualityFactor(0.9)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeType('application', 'atom+xml', new QualityFactor(0.8))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testBasicTypeWildcard()
    {
        $httpField  = 'text/*;q=0.8,application/xml;q=0.9';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeWildcardSubType('text', new QualityFactor(0.8)),
                new MimeType('text', 'html', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'xml', new QualityFactor(0.9)),
                new MimeType('application', 'xml', new QualityFactor(0.7))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeWildcardSubType('text', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.3))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testBasicSubtypeWildcard()
    {
        $httpField  = 'text/html;q=0.8,application/xml;q=0.9,*/*;q=0.5';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(0.8)),
                new MimeType('text', 'html', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeType('application', 'xml', new QualityFactor(0.9)),
                new MimeType('application', 'xml', new QualityFactor(0.7))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeWildcardType(new QualityFactor(0.5)),
                new MimeType('text', 'n3', new QualityFactor(0.3))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }


    public function testWildcardCombination()
    {
        $httpField  = '*/*;q=0.1,text/*;q=0.7,text/html;q=0.9';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $pairSort = new TypePairSort(
            new TypePair(
                new MimeAbsentType(new QualityFactor(0)),
                new MimeAbsentType(new QualityFactor(0))
            )
        );

        $expectCollection = new SharedTypePairCollection($pairSort);
        $expectCollection->addPair(
            new TypePair(
                new MimeType('text', 'html', new QualityFactor(0.9)),
                new MimeType('text', 'html', new QualityFactor(1))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeWildcardSubType('text', new QualityFactor(0.7)),
                new MimeType('text', 'n3', new QualityFactor(0.3))
            )
        );
        $expectCollection->addPair(
            new TypePair(
                new MimeWildcardType(new QualityFactor(0.1)),
                new MimeType('application', 'xml', new QualityFactor(0.7))
            )
        );

        $negotiate = new Negotiate();
        $resultCollection = $negotiate->mimeAll($httpField, $appPrefs);

        $this->assertEquals($expectCollection, $resultCollection);
    }
}
