<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
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
