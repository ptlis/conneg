<?php

/**
 * Test to verify the correctness of AbsentMimeType entities.
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

namespace ptlis\ConNeg\Test\Type\Mime;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;

class AbsentMimeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetType()
    {
        $type = new AbsentMimeType(new QualityFactor(0));

        $this->assertSame('', $type->getType());
        $this->assertSame('', $type->getMimeType());
        $this->assertSame('', $type->getMimeSubType());
        $this->assertSame(0, $type->getQualityFactor()->getFactor());
        $this->assertSame('', $type->__toString());
        $this->assertSame(-1, $type->getPrecedence());
    }


    public function testCloneCharsetType()
    {
        $type = new AbsentMimeType(new QualityFactor(0));

        $cloneType = clone $type;

        $this->assertEquals($type, $cloneType);
        $this->assertTrue($type == $cloneType);
        $this->assertFalse($type === $cloneType);
    }
}
