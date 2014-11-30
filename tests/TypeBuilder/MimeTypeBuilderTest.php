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
use ptlis\ConNeg\Type\Extens\AcceptExtens;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\TypeBuilder\MimeTypeBuilder;

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
            new QualityFactor(1),
            array(new AcceptExtens('4', 'level'))
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $real = $builder
            ->setAppType(true)
            ->setType('text/html')
            ->setQualityFactor(1)
            ->setAcceptExtens(array(array('level', '=', '4')))
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildInvalidType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"foo" is not a valid mime type'
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(true)
            ->setType('foo')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildAppWildcardInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not valid in application-provided types.'
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(true)
            ->setType('text/*')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildUserWildcardTypeOnlyInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"*/html" is not a valid mime type.'
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(false)
            ->setType('*/html')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildAppExtensInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Malformed accept-extens "foo=" found'
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $builder
            ->setAppType(true)
            ->setType('text/html')
            ->setQualityFactor(1)
            ->setAcceptExtens(array(array('foo', '=')))
            ->get();
    }

    public function testBuildUserExtensInvalid()
    {
        $expected = new MimeType(
            'text',
            'html',
            new QualityFactor(1)
        );

        $builder = new MimeTypeBuilder(new QualityFactorFactory());

        $real = $builder
            ->setAppType(false)
            ->setType('text/html')
            ->setQualityFactor(1)
            ->setAcceptExtens(array(array('foo', '=')))
            ->get();

        $this->assertEquals($expected, $real);
    }
}
