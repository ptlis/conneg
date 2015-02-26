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

namespace ptlis\ConNeg\Test\Type\Extens;

use ptlis\ConNeg\Type\Extens\AcceptExtens;

/**
 * Tests for AcceptExtens class.
 */
class AcceptExtensTest extends \PHPUnit_Framework_TestCase
{
    public function testNewAcceptExtensKeyValue()
    {
        $acceptExtens = new AcceptExtens('1', 'level');

        $this->assertSame(true, $acceptExtens->isCompound());
        $this->assertSame('level', $acceptExtens->getKey());
        $this->assertSame('1', $acceptExtens->getValue());
        $this->assertSame('level=1', $acceptExtens->__toString());
    }

    public function testNewAcceptExtensValueOnly()
    {
        $acceptExtens = new AcceptExtens('wibble');

        $this->assertSame(false, $acceptExtens->isCompound());
        $this->assertSame('', $acceptExtens->getKey());
        $this->assertSame('wibble', $acceptExtens->getValue());
        $this->assertSame('wibble', $acceptExtens->__toString());
    }
}
