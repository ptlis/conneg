<?php

/**
 * Test to verify the correctness of SharedTypePairCollection.
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

use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Charset\CharsetType;
use ptlis\ConNeg\TypePair\SharedTypePair;

class SharedTypePairCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetList()
    {
        $expectCollection = new SharedTypePairCollection();

        $typePairList = array();

        $typePairList[] = new SharedTypePair(
            new CharsetType('utf-8', new QualityFactor(1)),
            new CharsetType('utf-8', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new CharsetType('iso-8859-5', new QualityFactor(0.8)),
            new CharsetType('iso-8859-5', new QualityFactor(0.8))
        );

        $expectCollection->setList($typePairList);

        $this->assertEquals(2, count($expectCollection));
    }


    public function testAddPair()
    {
        $expectCollection = new SharedTypePairCollection();

        $expectCollection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );

        $this->assertEquals(2, count($expectCollection));
    }


    public function testIterator()
    {
        $expectCollection = new SharedTypePairCollection();

        $expectCollection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );
        $expectCollection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $typePair) {
            $this->assertInstanceOf('ptlis\ConNeg\TypePair\SharedTypePair', $typePair);
        }
    }


    public function testGetAscending()
    {
        $collection = new SharedTypePairCollection();

        $collection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );
        $collection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );

        $expectAppType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=1'
        );
        $expectUserType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=0.9'
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
        $collection = new SharedTypePairCollection();

        $collection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );

        $expectAppType = array(
            'utf-8;q=1',
            'iso-8859-5;q=0.8'
        );
        $expectUserType = array(
            'utf-8;q=0.9',
            'iso-8859-5;q=0.8'
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
        $collection = new SharedTypePairCollection();

        $collection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );

        $expectPair = new SharedTypePair(
            new CharsetType('utf-8', new QualityFactor(1)),
            new CharsetType('utf-8', new QualityFactor(0.9))
        );

        $bestPair = $collection->getBest();

        $this->assertEquals($expectPair, $bestPair);
    }


    public function testClone()
    {
        $collection = new SharedTypePairCollection();

        $collection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }


    public function testToString()
    {
        $collection = new SharedTypePairCollection();

        $collection->addPair(
            new SharedTypePair(
                new CharsetType('iso-8859-5', new QualityFactor(0.8)),
                new CharsetType('iso-8859-5', new QualityFactor(0.8))
            )
        );
        $collection->addPair(
            new SharedTypePair(
                new CharsetType('utf-8', new QualityFactor(1)),
                new CharsetType('utf-8', new QualityFactor(0.9))
            )
        );

        $this->assertEquals('iso-8859-5;q=0.64,utf-8;q=0.9', $collection->__toString());
    }
}
