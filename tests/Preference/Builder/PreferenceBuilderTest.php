<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Preference\Builder;

use ptlis\ConNeg\Preference\Builder\PreferenceBuilder;
use ptlis\ConNeg\Preference\Preference;

/**
 * Tests for standard type builder
 */
class PreferenceBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new Preference('utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::ENCODING)
            ->setFromServer(true)
            ->setVariant('utf-8')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAbsentTypeSuccess()
    {
        // Absent types should always have a quality factor of 0
        $expected = new Preference('', 0, Preference::ABSENT);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setVariant('')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildEmptyTypeSuccess()
    {
        $expected = new Preference('', 0, Preference::ABSENT);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientWildcardTypeSuccess()
    {
        $expected = new Preference('*', 0.8, Preference::WILDCARD);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setVariant('*')
            ->setQualityFactor(0.8)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildServerWildcardTypeInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            'Wildcards are not allowed in server-provided variants.'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setVariant('*')
            ->setQualityFactor(0.8)
            ->get();
    }

    public function testClientInvalidQualityFactorString()
    {
        $expected = new Preference('utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setVariant('utf-8')
            ->setQualityFactor('asdf')
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorString()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            'Invalid quality factor "asdf" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setVariant('utf-8')
            ->setQualityFactor('asdf')
            ->get();
    }

    public function testClientInvalidQualityFactorTooLarge()
    {
        $expected = new Preference('utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setVariant('utf-8')
            ->setQualityFactor(7)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorTooLarge()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            'Invalid quality factor "7" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setVariant('utf-8')
            ->setQualityFactor(7)
            ->get();
    }

    public function testClientInvalidQualityFactorTooSmall()
    {
        $expected = new Preference('utf-8', 0, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setVariant('utf-8')
            ->setQualityFactor(-1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorTooSmall()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidVariantException',
            'Invalid quality factor "-1" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setVariant('utf-8')
            ->setQualityFactor(-1)
            ->get();
    }

    public function testServerOmittedField()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The HTTP field must be provided to the builder.'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromServer(true)
            ->setVariant('utf-8')
            ->setQualityFactor(0.5)
            ->get();
    }
}
