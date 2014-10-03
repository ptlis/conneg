<?php

/**
 * Test to verify the correctness of SharedTypePairs
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

namespace ptlis\ConNeg\Test\TypePair;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;

class SharedTypePairTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetTypeOne()
    {
        $pair = new TypePair(
            new Type('utf-8', new QualityFactor(0.5)),
            new WildcardType(new QualityFactor(0.3))
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(1, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }


    public function testNewCharsetTypeTwo()
    {
        $pair = new TypePair(
            new WildcardType(new QualityFactor(0.3)),
            new Type('utf-8', new QualityFactor(0.5))
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(1, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }
}
