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

namespace ptlis\ConNeg\Test\Preference;

use ptlis\ConNeg\Preference\PreferenceCollection;
use ptlis\ConNeg\Preference\Preference;

class PreferenceCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $expectList = array(
            new Preference('text/html', 1, Preference::COMPLETE),
            new Preference('text/n3', 0.8, Preference::COMPLETE)
        );

        $expectCollection = new PreferenceCollection($expectList);

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $type) {
            $this->assertInstanceOf('ptlis\ConNeg\Preference\PreferenceInterface', $type);
        }
    }


    public function testGetAscendingOne()
    {
        $expectList = array(
            new Preference('text/html', 1, Preference::COMPLETE),
            new Preference('text/n3', 0.8, Preference::COMPLETE)
        );

        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/n3;q=0.8',
            'text/html;q=1'
        );

        $newCollection = $expectCollection->getAscending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testGetAscendingTwo()
    {
        $expectList = array(
            new Preference('text/n3', 0.8, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );
        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/n3;q=0.8',
            'text/html;q=1'
        );

        $newCollection = $expectCollection->getAscending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testGetAscendingThree()
    {
        $expectList = array(
            new Preference('text/n3', 1, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );

        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/html;q=1',
            'text/n3;q=1'
        );

        $newCollection = $expectCollection->getAscending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testGetDescendingOne()
    {
        $expectList = array(
            new Preference('text/n3', 0.8, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );
        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/html;q=1',
            'text/n3;q=0.8'
        );

        $newCollection = $expectCollection->getDescending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testGetDescendingTwo()
    {
        $expectList = array(
            new Preference('text/html', 1, Preference::COMPLETE),
            new Preference('text/n3', 0.8, Preference::COMPLETE)
        );

        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/html;q=1',
            'text/n3;q=0.8'
        );

        $newCollection = $expectCollection->getDescending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testGetDescendingThree()
    {
        $expectList = array(
            new Preference('text/n3', 1, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );

        $expectCollection = new PreferenceCollection($expectList);

        $expectType = array(
            'text/html;q=1',
            'text/n3;q=1'
        );

        $newCollection = $expectCollection->getDescending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectType[$i], $typePair->__toString());
            $i++;
        }
    }


    public function testClone()
    {
        $expectList = array(
            new Preference('text/n3', 0.8, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );

        $collection = new PreferenceCollection($expectList);

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }

    public function testToString()
    {
        $expectList = array(
            new Preference('text/n3', 0.8, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );

        $collection = new PreferenceCollection($expectList);

        $this->assertEquals('text/n3;q=0.8,text/html;q=1', $collection->__toString());
    }

    public function testCount()
    {
        $expectList = array(
            new Preference('text/n3', 0.8, Preference::COMPLETE),
            new Preference('text/html', 1, Preference::COMPLETE)
        );

        $collection = new PreferenceCollection($expectList);

        $this->assertEquals(2, count($collection));
    }
}
