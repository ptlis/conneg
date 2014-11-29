<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\TypeBuilder;

use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypeBuilder\TypeBuilder;

/**
 * Tests for standard type builder
 */
class StandardTypeBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new Type(
            'utf-8',
            new QualityFactor(1)
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $real = $builder
            ->setAppType(true)
            ->setType('utf-8')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAbsentTypeSuccess()
    {
        // Absent types should always have a quality factor of 0
        $expected = new AbsentType(
            new QualityFactor(0)
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $real = $builder
            ->setAppType(true)
            ->setType('')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildEmptyTypeSuccess()
    {
        $expected = new AbsentType(
            new QualityFactor(0)
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $real = $builder->getEmpty();

        $this->assertEquals($expected, $real);
    }

    public function testBuildUserWildcardTypeSuccess()
    {
        $expected = new WildcardType(
            new QualityFactor(0.8)
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $real = $builder
            ->setAppType(false)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAppWildcardTypeInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(true)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();
    }

    public function testBuildInvalidAbsentType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid type provided to builder.'
        );

        $builder = new TypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(true)
            ->setType(null)
            ->setQualityFactor(1)
            ->get();
    }
}
