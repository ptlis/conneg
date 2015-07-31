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

use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilder;

/**
 * Tests for standard type builder
 */
class PreferenceBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildTypeSuccess()
    {
        $expected = new Preference(Preference::ENCODING, 'utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::ENCODING)
            ->setFromServer(true)
            ->setType('utf-8')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildAbsentTypeSuccess()
    {
        // Absent types should always have a quality factor of 0
        $expected = new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setType('')
            ->setQualityFactor(1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildEmptyTypeSuccess()
    {
        $expected = new Preference(Preference::MIME, '', 0, Preference::ABSENT_TYPE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::MIME)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildClientWildcardTypeSuccess()
    {
        $expected = new Preference(Preference::LANGUAGE, '*', 0.8, Preference::WILDCARD);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testBuildServerWildcardTypeInvalid()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Wildcards are not allowed in server-provided types.'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setType('*')
            ->setQualityFactor(0.8)
            ->get();
    }

    public function testClientInvalidQualityFactorString()
    {
        $expected = new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setType('utf-8')
            ->setQualityFactor('asdf')
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorString()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid quality factor "asdf" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setType('utf-8')
            ->setQualityFactor('asdf')
            ->get();
    }

    public function testClientInvalidQualityFactorTooLarge()
    {
        $expected = new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setType('utf-8')
            ->setQualityFactor(7)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorTooLarge()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid quality factor "7" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setType('utf-8')
            ->setQualityFactor(7)
            ->get();
    }

    public function testClientInvalidQualityFactorTooSmall()
    {
        $expected = new Preference(Preference::LANGUAGE, 'utf-8', 0, Preference::COMPLETE);

        $builder = new PreferenceBuilder();

        $real = $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(false)
            ->setType('utf-8')
            ->setQualityFactor(-1)
            ->get();

        $this->assertEquals($expected, $real);
    }

    public function testServerInvalidQualityFactorTooSmall()
    {
        $this->setExpectedException(
            '\ptlis\ConNeg\Exception\InvalidTypeException',
            'Invalid quality factor "-1" in server preferences'
        );

        $builder = new PreferenceBuilder();

        $builder
            ->setFromField(Preference::LANGUAGE)
            ->setFromServer(true)
            ->setType('utf-8')
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
            ->setType('utf-8')
            ->setQualityFactor(0.5)
            ->get();
    }
}
