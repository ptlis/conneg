<?php

/**
 * Test to verify the correctness of Type entities.
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

namespace ptlis\ConNeg\Test\Type\Encoding;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Type;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetType()
    {
        $type = new Type('gzip', new QualityFactor(0.8));

        $this->assertSame('gzip', $type->getType());
        $this->assertSame(0.8, $type->getQualityFactor()->getFactor());
        $this->assertSame('gzip;q=0.8', $type->__toString());
        $this->assertSame(1, $type->getPrecedence());
    }


    public function testNewCharsetTypeOmitQualityFactor()
    {
        $type = new Type('deflate', new QualityFactor(1));

        $this->assertSame('deflate', $type->getType());
        $this->assertSame(1, $type->getQualityFactor()->getFactor());
        $this->assertSame('deflate;q=1', $type->__toString());
        $this->assertSame(1, $type->getPrecedence());
    }


    public function testCloneCharsetType()
    {
        $type = new Type('gzip', new QualityFactor(1));

        $cloneType = clone $type;

        $this->assertEquals($type, $cloneType);
        $this->assertTrue($type == $cloneType);
        $this->assertFalse($type === $cloneType);
    }
}
