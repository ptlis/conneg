<?php

/**
 * Test to verify the correctness of TypeCollection.
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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\MimeType;

class TypeCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetList()
    {
        $expectCollection = new TypeCollection();

        $typeList = array();

        $typeList[] = new MimeType('text', 'html', new QualityFactor(1));
        $typeList[] = new MimeType('text', 'n3', new QualityFactor(0.8));

        $expectCollection->setList($typeList);

        $this->assertEquals(2, count($expectCollection));
    }


    public function testAddPair()
    {
        $expectCollection = new TypeCollection();
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));

        $this->assertEquals(2, count($expectCollection));
    }


    public function testIterator()
    {
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $type) {
            $this->assertInstanceOf('ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface', $type);
        }
    }


    public function testGetAscendingOne()
    {
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));

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
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));

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
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));

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
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));

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
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));

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
        $expectCollection = new TypeCollection();

        $expectCollection->addType(new MimeType('text', 'n3', new QualityFactor(1)));
        $expectCollection->addType(new MimeType('text', 'html', new QualityFactor(1)));

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


    public function testCloneOne()
    {
        $collection = new TypeCollection();

        $collection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));
        $collection->addType(new MimeType('text', 'html', new QualityFactor(1)));

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }


    public function testCloneTwo()
    {
        $collection = new TypeCollection();

        $collection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));
        $collection->addType(new MimeType('text', 'html', new QualityFactor(1)));

        $expectCollection = $collection->getAscending();
        $expectCollection->addType(new MimeType('application', 'xml', new QualityFactor(1)));

        $this->assertNotSame($expectCollection, $collection);
    }


    public function testToString()
    {
        $collection = new TypeCollection();

        $collection->addType(new MimeType('text', 'n3', new QualityFactor(0.8)));
        $collection->addType(new MimeType('text', 'html', new QualityFactor(1)));


        $this->assertEquals('text/n3;q=0.8,text/html;q=1', $collection->__toString());
    }
}
