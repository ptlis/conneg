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

namespace ptlis\ConNeg\Test\Negotiation;

use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;

class NonStringAppTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testAppInvalidTypes()
    {
        $this->setExpectedException(
            '\LogicException',
            'Invalid application preferences passed to ptlis\ConNeg\Negotiation::parseAppPreferences'
        );

        $negotiate = new Negotiation();
        $negotiate->charsetAll('', new \DateTime());
    }
}
