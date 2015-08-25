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

namespace ptlis\ConNeg\Test\Preference\Builder;

use ptlis\ConNeg\Preference\Builder\MimePreferenceBuilder;
use ptlis\ConNeg\Preference\Preference;

/**
 * Tests for mime type builder
 */
class MimePreferenceBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new Preference(Preference::MIME, 'text/html', 1, Preference::COMPLETE);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setType('text/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientWildcardTypeOnlyInvalid()
    {
        $expected = new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(false)
            ->setType('*/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientTypeInvalid()
    {
        $expected = new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(false)
            ->setType('foo-bar')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildServerInvalidType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            '"foo" is not a valid mime type'
        );

        $builder = new MimePreferenceBuilder();

        $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setType('foo')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildServerWildcardInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not allowed in server-provided types.'
        );

        $builder = new MimePreferenceBuilder();

        $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setType('text/*')
            ->setQualityFactor(1)
            ->get();
    }

    public function testServerOmittedField()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The HTTP field must be provided to the builder.'
        );

        $builder = new MimePreferenceBuilder();

        $builder
            ->setFromServer(true)
            ->setType('text/html')
            ->setQualityFactor(0.5)
            ->get();
    }
}
