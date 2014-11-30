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

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\TypePair\TypePair;

class SharedTypePairCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectList = array(
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            ),
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            )
        );

        $expectCollection = new TypePairCollection($pairSort, $expectList);

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $typePair) {
            $this->assertInstanceOf('ptlis\ConNeg\TypePair\TypePair', $typePair);
        }
    }


    public function testGetAscending()
    {
        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expectList = array(
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            ),
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            )
        );

        $expectCollection = new TypePairCollection($pairSort, $expectList);

        $expectAppType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=1'
        );
        $expectUserType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=0.9'
        );

        $newCollection = $expectCollection->getAscending();

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
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            ),
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            )
        );
        $collection = new TypePairCollection($pairSort, $pairList);

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
        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            ),
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            )
        );
        $collection = new TypePairCollection($pairSort, $pairList);

        $expectPair = new TypePair(
            new Type('utf-8', new QualityFactor(0.9)),
            new Type('utf-8', new QualityFactor(1))
        );

        $bestPair = $collection->getBest();

        $this->assertEquals($expectPair, $bestPair);
    }


    public function testClone()
    {
        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            ),
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            )
        );
        $collection = new TypePairCollection($pairSort, $pairList);

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }


    public function testToString()
    {
        $pairSort = new TypePairSort(
            new TypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $pairList = array(
            new TypePair(
                new Type('iso-8859-5', new QualityFactor(0.8)),
                new Type('iso-8859-5', new QualityFactor(0.8))
            ),
            new TypePair(
                new Type('utf-8', new QualityFactor(0.9)),
                new Type('utf-8', new QualityFactor(1))
            )
        );
        $collection = new TypePairCollection($pairSort, $pairList);

        $this->assertEquals('iso-8859-5;q=0.64,utf-8;q=0.9', $collection->__toString());
    }
}
