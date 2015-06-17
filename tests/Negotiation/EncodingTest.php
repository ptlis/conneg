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

use ptlis\ConNeg\Collection\CollectionInterface;
use ptlis\ConNeg\Negotiation;
use ptlis\ConNeg\Type\TypeInterface;

class EncodingTest extends AbstractNegotiationTest
{
    /**
     * @dataProvider encodingProvider
     *
     * @param string $userField
     * @param string $appField
     * @param TypeInterface $best
     * @param CollectionInterface $all
     */
    public function testBest($userField, $appField, TypeInterface $best, CollectionInterface $all)
    {
        $negotiate = new Negotiation();
        $resultType = $negotiate->encodingBest($userField, $appField);

        $this->assertEquals($best, $resultType);
    }

    /**
     * @dataProvider encodingProvider
     *
     * @param string $userField
     * @param string $appField
     * @param TypeInterface $best
     * @param CollectionInterface $all
     */
    public function testAll($userField, $appField, TypeInterface $best, CollectionInterface $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->encodingAll($userField, $appField);

        $this->assertEquals($all, $collection);
    }
}
