<?php

/**
 * Test to verify the correctness of TypePairSort.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Collection;

use ptlis\ConNeg\Collection\TypePairSort;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

class TypePairSortTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAscendingOne()
    {
        $typePairList = array();
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.9, Type::EXACT_TYPE),
            new Type('en-gb', 1, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.8, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.9, Type::EXACT_TYPE),
            new Type('en-gb', 0.5, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.8, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.8, Type::EXACT_TYPE),
            new Type('en-gb', 0.9, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.8, Type::EXACT_TYPE),
            new Type('fr', 0.9, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.9, Type::EXACT_TYPE),
            new Type('en-gb', 0.8, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.8, Type::EXACT_TYPE),
            new Type('fr', 0.9, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.8, Type::EXACT_TYPE),
            new Type('en-gb', 0.9, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.9, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('', 0, Type::ABSENT_TYPE),
            new Type('en-gb', 0.9, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('', 0, Type::ABSENT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.9, Type::EXACT_TYPE),
            new Type('', 0, Type::ABSENT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.8, Type::EXACT_TYPE),
            new Type('', 0, Type::ABSENT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortAscending($typePairList);

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
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.8, Type::EXACT_TYPE),
            new Type('en-gb', 0.9, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.9, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $newCollection = $sort->sortDescending($typePairList);

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
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $best = $sort->getBest(
            array(),
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $expect = new TypePair(
            new Type('', 0, Type::ABSENT_TYPE),
            new Type('', 0, Type::ABSENT_TYPE)
        );

        $this->assertEquals($expect, $best);
    }


    public function testGetBest()
    {
        $sort = new TypePairSort(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $typePairList = array();
        $typePairList[] = new TypePair(
            new Type('en-gb', 0.8, Type::EXACT_TYPE),
            new Type('en-gb', 0.9, Type::EXACT_TYPE)
        );
        $typePairList[] = new TypePair(
            new Type('fr', 0.9, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $best = $sort->getBest(
            $typePairList,
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            )
        );

        $expect = new TypePair(
            new Type('fr', 0.9, Type::EXACT_TYPE),
            new Type('fr', 0.8, Type::EXACT_TYPE)
        );

        $this->assertEquals($expect, $best);
    }
}
