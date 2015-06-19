<?php

/**
 * Test to verify the correctness of MimeType entities.
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

use ptlis\ConNeg\Type\MimeType;

class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetType()
    {
        $type = new MimeType('text', 'html', 0.8);

        $this->assertSame('text/html', $type->getType());
        $this->assertSame('text', $type->getMimeType());
        $this->assertSame('html', $type->getMimeSubType());
        $this->assertSame(0.8, $type->getQualityFactor());
        $this->assertSame('text/html;q=0.8', $type->__toString());
        $this->assertSame(2, $type->getPrecedence());
        $this->assertSame(array(), $type->getExtens());
    }


    public function testNewCharsetTypeOmitQualityFactor()
    {
        $type = new MimeType('application', 'xml', 1);

        $this->assertSame('application/xml', $type->getType());
        $this->assertSame('application', $type->getMimeType());
        $this->assertSame('xml', $type->getMimeSubType());
        $this->assertSame(1, $type->getQualityFactor());
        $this->assertSame('application/xml;q=1', $type->__toString());
        $this->assertSame(2, $type->getPrecedence());
        $this->assertSame(array(), $type->getExtens());
    }


    public function testCloneCharsetType()
    {
        $type = new MimeType('text', 'html', 1);

        $cloneType = clone $type;

        $this->assertEquals($type, $cloneType);
        $this->assertTrue($type == $cloneType);
        $this->assertFalse($type === $cloneType);
        $this->assertSame(array(), $type->getExtens());
    }
}
