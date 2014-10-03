<?php

/**
 * Test to verify the correctness of TypePairSort.
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
use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\SharedTypePair;

class TypePairSortTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAscendingOne()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.9)),
            new Type('en-gb', new QualityFactor(1))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.8)),
            new Type('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'fr;q=0.8',
            'en-gb;q=1'
        );

        $expectUserType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }

    public function testGetAscendingTwo()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.9)),
            new Type('en-gb', new QualityFactor(0.5))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.8)),
            new Type('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'en-gb;q=0.5',
            'fr;q=0.8'
        );

        $expectUserType = array(
            'en-gb;q=0.9',
            'fr;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }



    public function testGetAscendingThree()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.8)),
            new Type('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.8)),
            new Type('fr', new QualityFactor(0.9))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'fr;q=0.9',
            'en-gb;q=0.9'
        );

        $expectUserType = array(
            'fr;q=0.8',
            'en-gb;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetAscendingFour()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.9)),
            new Type('en-gb', new QualityFactor(0.8))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.8)),
            new Type('fr', new QualityFactor(0.9))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'fr;q=0.9',
            'en-gb;q=0.8'
        );

        $expectUserType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetAscendingFive()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.8)),
            new Type('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.9)),
            new Type('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'en-gb;q=0.9',
            'fr;q=0.8'
        );

        $expectUserType = array(
            'en-gb;q=0.8',
            'fr;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetAscendingSix()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new AbsentType(new QualityFactor(0)),
            new Type('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $expectUserType = array(
            '',
            ''
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetAscendingSeven()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.9)),
            new AbsentType(new QualityFactor(0))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.8)),
            new AbsentType(new QualityFactor(0))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortAscending($typePairList, $newCollection);

        $expectAppType = array(
            '',
            ''
        );

        $expectUserType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetDescending()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.8)),
            new Type('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.9)),
            new Type('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $newCollection = new SharedTypePairCollection($sort);
        $sort->sortDescending($typePairList, $newCollection);

        $expectAppType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $expectUserType = array(
            'fr;q=0.9',
            'en-gb;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectAppType[$i], $typePair->getAppType()->__toString());
            $this->assertSame($expectUserType[$i], $typePair->getUserType()->__toString());
            $i++;
        }
    }


    public function testGetBestEmpty()
    {
        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $best = $sort->getBest(
            array(),
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expect = new SharedTypePair(new AbsentType(new QualityFactor(0)), new AbsentType(new QualityFactor(0)));

        $this->assertEquals($expect, $best);
    }


    public function testGetBest()
    {
        $sort = new TypePairSort(
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new Type('en-gb', new QualityFactor(0.8)),
            new Type('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new Type('fr', new QualityFactor(0.9)),
            new Type('fr', new QualityFactor(0.8))
        );

        $best = $sort->getBest(
            $typePairList,
            new SharedTypePair(
                new AbsentType(new QualityFactor(0)),
                new AbsentType(new QualityFactor(0))
            )
        );

        $expect = new SharedTypePair(
            new Type('fr', new QualityFactor(0.9)),
            new Type('fr', new QualityFactor(0.8))
        );

        $this->assertEquals($expect, $best);
    }
}
