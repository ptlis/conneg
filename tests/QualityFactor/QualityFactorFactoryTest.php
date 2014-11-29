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

        $qFactor = $qualityFactorFactory->get(0.5, true);

        $this->assertEquals($expected, $qFactor);
    }

    public function testAbsentQualityFactor()
    {
        $val = '';

        $expected = new QualityFactor(1);

        $qualityFactorFactory = new QualityFactorFactory();

        $qFactor = $qualityFactorFactory->get($val, false);

        $this->assertEquals($expected, $qFactor);
    }

    public function testAppMalformed()
    {
        $val = 'bob';

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\QualityFactorMalformedException',
            'Invalid quality factor of "' . $val . '" provided, must be between 0 and 1 (inclusive)'
        );

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactorFactory->get($val, true);
    }


    public function testAppTooLarge()
    {
        $val = 10;

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\QualityFactorTooLargeException',
            'Invalid quality factor of "' . $val . '" provided, must be between 0 and 1 (inclusive)'
        );

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactorFactory->get($val, true);
    }


    public function testAppNegative()
    {
        $val = -1;

        $this->setExpectedException(
            'ptlis\ConNeg\Exception\QualityFactorNegativeException',
            'Invalid quality factor of "' . $val . '" provided, must be between 0 and 1 (inclusive)'
        );

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactorFactory->get($val, true);
    }


    public function testUserMalformed()
    {
        $val = 'bob';

        $expected = new QualityFactor(1);

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactor = $qualityFactorFactory->get($val, false);

        $this->assertEquals($expected, $qualityFactor);
    }


    public function testUserTooLarge()
    {
        $val = 10;

        $expected = new QualityFactor(1);

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactor = $qualityFactorFactory->get($val, false);

        $this->assertEquals($expected, $qualityFactor);
    }


    public function testUserNegative()
    {
        $val = -1;

        $expected = new QualityFactor(0);

        $qualityFactorFactory = new QualityFactorFactory();

        $qualityFactor = $qualityFactorFactory->get($val, false);

        $this->assertEquals($expected, $qualityFactor);
    }
}
