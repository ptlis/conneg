<?php

/**
 * Test to verify the correctness of MimeWildcardSubType entities.
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

namespace ptlis\ConNeg\Test\Type\Mime;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\MimeWildcardSubType;

class MimeWildcardSubTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetType()
    {
        $type = new MimeWildcardSubType('text', new QualityFactor(0.75));

        $this->assertSame('text/*', $type->getType());
        $this->assertSame('text', $type->getMimeType());
        $this->assertSame('*', $type->getMimeSubType());
        $this->assertSame(0.75, $type->getQualityFactor()->getFactor());
        $this->assertSame('text/*;q=0.75', $type->__toString());
        $this->assertSame(1, $type->getPrecedence());
    }


    public function testNewCharsetTypeOmitQualityFactor()
    {
        $type = new MimeWildcardSubType('application', new QualityFactor(1));

        $this->assertSame('application/*', $type->getType());
        $this->assertSame('application', $type->getMimeType());
        $this->assertSame('*', $type->getMimeSubType());
        $this->assertSame(1, $type->getQualityFactor()->getFactor());
        $this->assertSame('application/*;q=1', $type->__toString());
        $this->assertSame(1, $type->getPrecedence());
    }


    public function testCloneCharsetType()
    {
        $type = new MimeWildcardSubType('text', new QualityFactor(1));

        $cloneType = clone $type;

        $this->assertEquals($type, $cloneType);
        $this->assertTrue($type == $cloneType);
        $this->assertFalse($type === $cloneType);
    }
}
