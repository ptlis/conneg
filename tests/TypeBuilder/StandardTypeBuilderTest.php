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

namespace ptlis\ConNeg\Test\TypeBuilder;

use ptlis\ConNeg\Type\Type;
use ptlis\ConNeg\Type\Builder\TypeBuilder;

/**
 * Tests for standard type builder
 */
class StandardTypeBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new Type(
            'utf-8',
            1,
            Type::EXACT_TYPE
        );

        $builder = new TypeBuilder();

        $real = $builder
            ->setFromApp(true)
            ->setType('utf-8')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAbsentTypeSuccess()
    {
        // Absent types should always have a quality factor of 0
        $expected = new Type('', 0, Type::ABSENT_TYPE);

        $builder = new TypeBuilder();

        $real = $builder
            ->setFromApp(true)
            ->setType('')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildEmptyTypeSuccess()
    {
        $expected = new Type('', 0, Type::ABSENT_TYPE);

        $builder = new TypeBuilder();

        $real = $builder->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildUserWildcardTypeSuccess()
    {
        $expected = new Type('*', 0.8, Type::WILDCARD_TYPE);

        $builder = new TypeBuilder();

        $real = $builder
            ->setFromApp(false)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAppWildcardTypeInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not allowed in application-provided types.'
        );

        $builder = new TypeBuilder();

        $builder
            ->setFromApp(true)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();
    }
}
