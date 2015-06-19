<?php

/**
 * Test to verify the correctness of SharedTypePairs
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

namespace ptlis\ConNeg\Test\TypePair;

use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

class SharedTypePairTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetTypeOne()
    {
        $pair = new TypePair(
            new Type('utf-8', 0.5, Type::EXACT_TYPE),
            new Type('*', 0.3, Type::WILDCARD_TYPE)
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }


    public function testNewCharsetTypeTwo()
    {
        $pair = new TypePair(
            new Type('*', 0.3, Type::WILDCARD_TYPE),
            new Type('utf-8', 0.5, Type::EXACT_TYPE)
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }
}
