<?php

/**
 * Test to verify the correctness of TypeCollection.
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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\MimeType;

class TypeCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $expectList = array(
            new MimeType('text', 'html', new QualityFactor(1)),
            new MimeType('text', 'n3', new QualityFactor(0.8))
        );

        $expectCollection = new TypeCollection($expectList);

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $type) {
            $this->assertInstanceOf('ptlis\ConNeg\Type\TypeInterface', $type);
        }
    }


    public function testGetAscendingOne()
    {
        $expectList = array(
            new MimeType('text', 'html', new QualityFactor(1)),
            new MimeType('text', 'n3', new QualityFactor(0.8))
        );

        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );
        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'n3', new QualityFactor(1)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );
        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'html', new QualityFactor(1)),
            new MimeType('text', 'n3', new QualityFactor(0.8))
        );

        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'n3', new QualityFactor(1)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $expectCollection = new TypeCollection($expectList);

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
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $collection = new TypeCollection($expectList);

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }

    public function testToString()
    {
        $expectList = array(
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $collection = new TypeCollection($expectList);

        $this->assertEquals('text/n3;q=0.8,text/html;q=1', $collection->__toString());
    }

    public function testCount()
    {
        $expectList = array(
            new MimeType('text', 'n3', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $collection = new TypeCollection($expectList);

        $this->assertEquals(2, count($collection));
    }
}
