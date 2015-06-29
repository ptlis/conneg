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

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\TypePair\TypePair;

class NonStringAppTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testAppInvalidTypes()
    {
        $this->setExpectedException(
            '\LogicException',
            'Invalid application preferences passed to ptlis\ConNeg\Negotiation::sharedAppPrefsToTypes'
        );

        $negotiate = new Negotiation();
        $negotiate->charsetAll('', new \DateTime());
    }

    public function testAppCollection()
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->charsetAll('', new TypeCollection(array()));

        $this->assertEquals(
            new TypePair(
                new Type('', 0, Type::ABSENT_TYPE),
                new Type('', 0, Type::ABSENT_TYPE)
            ),
            $collection->getBest()
        );
    }
}
