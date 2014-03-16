<?php

/** *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Test\Type;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\QualityFactor\QualityFactorFactory;
use ptlis\ConNeg\Type\Charset\CharsetType;
use ptlis\ConNeg\Type\Charset\CharsetTypeBuilder;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\Type\Mime\MimeTypeBuilder;
use ptlis\ConNeg\Type\Mime\MimeTypeFactory;
use ptlis\ConNeg\Type\Mime\MimeTypeRegexProvider;
use ptlis\ConNeg\Type\Shared\SharedTypeFactory;
use ptlis\ConNeg\Type\Shared\SharedTypeRegexProvider;

/**
 * Test to verify correct handling of malformed client types.
 */
class MalformedQualityFactorTest extends \PHPUnit_Framework_TestCase
{
    public function testTooLargeUserCharsetOne()
    {
        $field = 'utf-8;q=1.5,iso-8859-5;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new CharsetType(
                    'utf-8',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new CharsetType(
                    'iso-8859-5',
                    new QualityFactor(0.5)
                )
            );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testTooLargeUserCharsetTwo()
    {
        $field = 'utf-8;q=15,iso-8859-5;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new CharsetType(
                    'utf-8',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new CharsetType(
                    'iso-8859-5',
                    new QualityFactor(0.5)
                )
            );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testTooLargeAppCharsetOne()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "1.5" provided, must be between 0 and 1 (inclusive)'
        );

        $field = 'utf-8;q=1.5,iso-8859-5;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new CharsetType(
                    'utf-8',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new CharsetType(
                    'iso-8859-5',
                    new QualityFactor(0.5)
                )
            );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp($field);
    }


    public function testTooLargeAppCharsetTwo()
    {
        $this->setExpectedException(
            'ptlis\ConNeg\Exception\ConNegException',
            'Invalid quality factor of "15" provided, must be between 0 and 1 (inclusive)'
        );

        $field = 'utf-8;q=15,iso-8859-5;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new CharsetType(
                    'utf-8',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new CharsetType(
                    'iso-8859-5',
                    new QualityFactor(0.5)
                )
            );

        $factory = new SharedTypeFactory(
            new SharedTypeRegexProvider(),
            new CharsetTypeBuilder(new QualityFactorFactory())
        );

        $factory->parseApp($field);
    }


//    public function testInvalidTooLow()
//    {
//        $qFactor = -1;
//
//        $this->setExpectedException(
//            'ptlis\ConNeg\Exception\ConNegException',
//            'Invalid quality factor of "' . $qFactor . '" provided, must be between 0 and 1 (inclusive)'
//        );
//
//        $qualityFactorFactory = new QualityFactorFactory();
//
//        $qualityFactorFactory->get($qFactor);
//    }
//
//
//    public function testInvalidTooHigh()
//    {
//        $qFactor = 1.5;
//
//        $this->setExpectedException(
//            'ptlis\ConNeg\Exception\ConNegException',
//            'Invalid quality factor of "' . $qFactor . '" provided, must be between 0 and 1 (inclusive)'
//        );
//
//        $qualityFactorFactory = new QualityFactorFactory();
//
//        $qualityFactorFactory->get($qFactor);
//    }


    public function testTooLargeUserMimeOne()
    {
        $field = 'text/html;q=1.5,text/plain;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new MimeType(
                    'text',
                    'html',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new MimeType(
                    'text',
                    'plain',
                    new QualityFactor(0.5)
                )
            );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }


    public function testTooLargeUserMimeTwo()
    {
        $field = 'text/html;q=15,text/plain;q=0.5';

        $expectCollection = new TypeCollection();
        $expectCollection
            ->addType(
                new MimeType(
                    'text',
                    'html',
                    new QualityFactor(1)
                )
            )
            ->addType(
                new MimeType(
                    'text',
                    'plain',
                    new QualityFactor(0.5)
                )
            );

        $factory = new MimeTypeFactory(
            new MimeTypeRegexProvider(),
            new MimeTypeBuilder(new QualityFactorFactory())
        );

        $this->assertEquals($expectCollection, $factory->parseUser($field));
    }
}
