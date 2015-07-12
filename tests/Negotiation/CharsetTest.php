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
use ptlis\ConNeg\Preference\Matched\CollectionInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

class CharsetDataProvider extends NegotiationDataProvider
{
    /**
     * @dataProvider charsetProvider
     *
     * @param string $userField
     * @param string $appField
     * @param PreferenceInterface $best
     * @param CollectionInterface $all
     */
    public function testBest($userField, $appField, PreferenceInterface $best, CollectionInterface $all)
    {
        $negotiate = new Negotiation();
        $resultType = $negotiate->charsetBest($userField, $appField);

        $this->assertEquals($best, $resultType);
    }

    /**
     * @dataProvider charsetProvider
     *
     * @param string $userField
     * @param string $appField
     * @param PreferenceInterface $best
     * @param CollectionInterface $all
     */
    public function testAll($userField, $appField, PreferenceInterface $best, CollectionInterface $all)
    {
        $negotiate = new Negotiation();
        $collection = $negotiate->charsetAll($userField, $appField);

        $this->assertEquals($all, $collection);
    }
}
