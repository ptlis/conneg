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

namespace ptlis\ConNeg\Test\Preference\Matched;

use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;

class SharedMatchedPreferencesTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetTypeOne()
    {
        $pair = new MatchedPreferences(
            new Preference('utf-8', 0.5, Preference::COMPLETE),
            new Preference('*', 0.3, Preference::WILDCARD)
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }


    public function testNewCharsetTypeTwo()
    {
        $pair = new MatchedPreferences(
            new Preference('*', 0.3, Preference::WILDCARD),
            new Preference('utf-8', 0.5, Preference::COMPLETE)
        );

        $this->assertSame('utf-8', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }
}
