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
use ptlis\ConNeg\Type\Language\LanguageType;
use ptlis\ConNeg\TypePair\SharedTypePair;

class TypePairSortTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAscendingOne()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new LanguageType('en-gb', new QualityFactor(1)),
            new LanguageType('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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
            new LanguageType('en-gb', new QualityFactor(0.5)),
            new LanguageType('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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
            new LanguageType('en-gb', new QualityFactor(0.9)),
            new LanguageType('en-gb', new QualityFactor(0.8))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.9)),
            new LanguageType('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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
            new LanguageType('en-gb', new QualityFactor(0.8)),
            new LanguageType('en-gb', new QualityFactor(0.9))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.9)),
            new LanguageType('fr', new QualityFactor(0.8))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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
            new LanguageType('en-gb', new QualityFactor(0.9)),
            new LanguageType('en-gb', new QualityFactor(0.8))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.9))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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


    public function testGetDescending()
    {
        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new LanguageType('en-gb', new QualityFactor(0.9)),
            new LanguageType('en-gb', new QualityFactor(0.8))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.9))
        );

        $sort = new TypePairSort();

        $newCollection = new SharedTypePairCollection();
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
        $sort = new TypePairSort();

        $best = $sort->getBest(array(), new SharedTypePair(new AbsentType(), new AbsentType()));

        $expect = new SharedTypePair(new AbsentType(), new AbsentType());

        $this->assertEquals($expect, $best);
    }


    public function testGetBest()
    {
        $sort = new TypePairSort();

        $typePairList = array();
        $typePairList[] = new SharedTypePair(
            new LanguageType('en-gb', new QualityFactor(0.9)),
            new LanguageType('en-gb', new QualityFactor(0.8))
        );
        $typePairList[] = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.9))
        );

        $best = $sort->getBest($typePairList, new SharedTypePair(new AbsentType(), new AbsentType()));

        $expect = new SharedTypePair(
            new LanguageType('fr', new QualityFactor(0.8)),
            new LanguageType('fr', new QualityFactor(0.9))
        );

        $this->assertEquals($expect, $best);
    }
}