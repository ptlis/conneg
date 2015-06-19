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

namespace ptlis\ConNeg\Test\Type;

use ptlis\ConNeg\Type\MimeType;

class MimeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $type = new MimeType('text', 'html', 0.5, MimeType::EXACT_TYPE);

        $this->assertSame('text/html', $type->getType());
        $this->assertSame(0.5, $type->getQualityFactor());
        $this->assertSame('text/html;q=0.5', $type->__toString());
        $this->assertSame(2, $type->getPrecedence());
    }
}
