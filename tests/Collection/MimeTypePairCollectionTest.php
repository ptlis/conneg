<?php

/**
 * Test to verify the correctness of MimeTypePairCollection.
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

namespace ptlis\ConNeg\Test\Collection;

use ptlis\ConNeg\Collection\MimeTypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\TypePair\MimeTypePair;

class MimeTypePairCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetList()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $expectCollection = new MimeTypePairCollection($pairSort);

        $typePairList = array();

        $typePairList[] = new MimeTypePair(
            new MimeType('text', 'html', new QualityFactor(1)),
            new MimeType('text', 'html', new QualityFactor(0.9))
        );
        $typePairList[] = new MimeTypePair(
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'n3', new QualityFactor(0.8))
        );

        $expectCollection->setList($typePairList);

        $this->assertEquals(2, count($expectCollection));
    }


    public function testAddPair()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $expectCollection = new MimeTypePairCollection($pairSort);

        $expectCollection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );

        $this->assertEquals(2, count($expectCollection));
    }


    public function testIterator()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $expectCollection = new MimeTypePairCollection($pairSort);

        $expectCollection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $typePair) {
            $this->assertInstanceOf('ptlis\ConNeg\TypePair\MimeTypePair', $typePair);
        }
    }


    public function testGetAscending()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $collection = new MimeTypePairCollection($pairSort);

        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );
        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );

        $expectAppType = array(
            'text/n3;q=0.8',
            'text/html;q=1'
        );
        $expectUserType = array(
            'text/n3;q=0.8',
            'text/html;q=0.9'
        );

        $newCollection = $collection->getAscending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetDescending()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $collection = new MimeTypePairCollection($pairSort);

        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );

        $expectAppType = array(
            'text/html;q=1',
            'text/n3;q=0.8'
        );
        $expectUserType = array(
            'text/html;q=0.9',
            'text/n3;q=0.8'
        );

        $newCollection = $collection->getDescending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetBest()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $collection = new MimeTypePairCollection($pairSort);

        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );

        $expectPair = new MimeTypePair(
            new MimeType('text', 'html', new QualityFactor(1)),
            new MimeType('text', 'html', new QualityFactor(0.9))
        );

        $bestPair = $collection->getBest();

        $this->assertEquals($expectPair, $bestPair);
    }


    public function testClone()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $collection = new MimeTypePairCollection($pairSort);

        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }


    public function testToString()
    {
        $pairSort = new TypePairSort(
            new MimeTypePair(
                new AbsentMimeType(new QualityFactor(0)),
                new AbsentMimeType(new QualityFactor(0))
            )
        );

        $collection = new MimeTypePairCollection($pairSort);

        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'n3', new QualityFactor(0.8)),
                new MimeType('text', 'n3', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new MimeTypePair(
                new MimeType('text', 'html', new QualityFactor(1)),
                new MimeType('text', 'html', new QualityFactor(0.9))
            )
        );

        $this->assertEquals('text/n3;q=0.64,text/html;q=0.9', $collection->__toString());
    }
}
