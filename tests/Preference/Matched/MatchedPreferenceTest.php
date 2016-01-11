<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Preference\Matched;

use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;

class MatchedPreferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetTypeOne()
    {
        $pair = new MatchedPreference(
            Preference::CHARSET,
            new Preference('utf-8', 0.5, Preference::COMPLETE),
            new Preference('*', 0.3, Preference::WILDCARD)
        );

        $this->assertSame('utf-8', $pair->getVariant());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }


    public function testNewCharsetTypeTwo()
    {
        $pair = new MatchedPreference(
            Preference::CHARSET,
            new Preference('*', 0.3, Preference::WILDCARD),
            new Preference('utf-8', 0.5, Preference::COMPLETE)
        );

        $this->assertSame('utf-8', $pair->getVariant());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('utf-8;q=0.15', $pair->__toString());
    }
}
