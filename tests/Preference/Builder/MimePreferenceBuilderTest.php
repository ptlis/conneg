<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
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
        $expected = new Preference('text/html', 1, Preference::COMPLETE);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setVariant('text/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientWildcardTypeOnlyInvalid()
    {
        $expected = new Preference('', 0, Preference::ABSENT);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(false)
            ->setVariant('*/html')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientTypeInvalid()
    {
        $expected = new Preference('', 0, Preference::ABSENT);

        $builder = new MimePreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(false)
            ->setVariant('foo-bar')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildServerInvalidType()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            '"foo" is not a valid mime type'
        );

        $builder = new MimePreferenceBuilder();

        $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setVariant('foo')
            ->setQualityFactor(1)
            ->get();
    }

    public function testBuildServerWildcardInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            'Wildcards are not allowed in server-provided variants.'
        );

        $builder = new MimePreferenceBuilder();

        $builder
            ->setFromField(Preference::MIME)
            ->setFromServer(true)
            ->setVariant('text/*')
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
            ->setVariant('text/html')
            ->setQualityFactor(0.5)
            ->get();
    }
}
