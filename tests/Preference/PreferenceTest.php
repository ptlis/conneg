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

namespace ptlis\ConNeg\Test\Preference;

use ptlis\ConNeg\Preference\Preference;

class PreferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $type = new Preference('utf-8', 1, Preference::COMPLETE);

        $this->assertSame('utf-8', $type->getVariant());
        $this->assertSame(1, $type->getQualityFactor());
        $this->assertSame('utf-8;q=1', $type->__toString());
        $this->assertSame(2, $type->getPrecedence());
    }
}
