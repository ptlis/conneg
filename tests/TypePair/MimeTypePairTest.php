<?php

/**
 * Test to verify the correctness of MimeTypePairs
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\TypePair;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\Type\Mime\MimeWildcardType;
use ptlis\ConNeg\TypePair\MimeTypePair;

class MimeTypePairTest extends \PHPUnit_Framework_TestCase
{
    public function testNewCharsetTypeOne()
    {
        $pair = new MimeTypePair(
            new MimeType('text', 'html', new QualityFactor(0.5)),
            new MimeWildcardType(new QualityFactor(0.3))
        );

        $this->assertSame('text/html', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('text/html;q=0.15', $pair->__toString());
    }


    public function testNewCharsetTypeTwo()
    {
        $pair = new MimeTypePair(
            new MimeType('text', 'html', new QualityFactor(0.5)),
            new MimeWildcardType(new QualityFactor(0.3))
        );

        $this->assertSame('text/html', $pair->getType());
        $this->assertSame(2, $pair->getPrecedence());
        $this->assertSame('text/html;q=0.15', $pair->__toString());
    }
}
