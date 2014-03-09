<?php

/**
 * Test to verify the correctness of QualityFactorFactory.
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

namespace ptlis\ConNeg\Test\QualityFactor;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;

class QualityFactorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        $expected = new QualityFactor(0.5);

        $qualityFactorFactory = new QualityFactorFactory();

        $qFactor = $qualityFactorFactory->get(0.5);

        $this->assertEquals($expected, $qFactor);
    }


    public function testInvalidTooLow()
    {
        $qFactor = -1;

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "' . $qFactor . '" provided, must be between 0 and 1 (inclusive)'
        );

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactorFactory->get($qFactor);
    }


    public function testInvalidTooHigh()
    {
        $qFactor = 1.5;

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "' . $qFactor . '" provided, must be between 0 and 1 (inclusive)'
        );

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactorFactory->get($qFactor);
    }
}
