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

use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\Builder\MimeTypeBuilder;

/**
 * Tests for mime type builder
 */
class MimeTypeBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new MimeType(
            'text',
            'html',
            1,
            MimeType::EXACT_TYPE
        );

        $builder = new MimeTypeBuilder();

        $real = $builder
            ->setFromApp(true)
            ->setType('text/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildUserWildcardTypeOnlyInvalid()
    {
        $expected = new MimeType(
            '',
            '',
            0,
            MimeType::ABSENT_TYPE
        );

        $builder = new MimeTypeBuilder();

        $real = $builder
            ->setFromApp(false)
            ->setType('*/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildUserTypeInvalid()
    {
        $expected = new MimeType(
            '',
            '',
            0,
            MimeType::ABSENT_TYPE
        );

        $builder = new MimeTypeBuilder();

        $real = $builder
            ->setFromApp(false)
            ->setType('foo-bar')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAppInvalidType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"foo" is not a valid mime type'
        );

        $builder = new MimeTypeBuilder();

        $builder
            ->setFromApp(true)
            ->setType('foo')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildAppWildcardInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not allowed in application-provided types.'
        );

        $builder = new MimeTypeBuilder();

        $builder
            ->setFromApp(true)
            ->setType('text/*')
            ->setQualityFactor(1)
            ->get();
    }
}
