<?php

/**
 * Test to verify the use of the Negotiate::mimeBest method.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source coapplication/xml.
 */

namespace ptlis\ConNeg\Test\Negotiate;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Negotiate;
use ptlis\ConNeg\QualityFactor\QualityFactor;
use ptlis\ConNeg\Type\Mime\AbsentMimeType;
use ptlis\ConNeg\Type\Mime\MimeType;
use ptlis\ConNeg\Type\Mime\MimeWildcardSubType;
use ptlis\ConNeg\TypePair\SharedTypePair;

class MimeBestTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAndAppEmpty()
    {
        $httpField  = '';
        $appPrefs   = '';

        $expectPair = new SharedTypePair(
            new AbsentMimeType(new QualityFactor(0)),
            new AbsentMimeType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserAndAppEmptyCollection()
    {
        $httpField  = '';
        $appPrefs   = new TypeCollection();

        $expectPair = new SharedTypePair(
            new AbsentMimeType(new QualityFactor(0)),
            new AbsentMimeType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserAndAppInvalid()
    {
        $httpField  = '';
        $appPrefs   = new \StdClass();

        $this->setExpectedException(
            'Exception',
            'invalid application preferences passed to ptlis\ConNeg\Negotiate::ptlis\ConNeg\Negotiate::sharedAppPrefsToTypes'
        );

        $negotiate = new Negotiate();
        $negotiate->mimeBest($httpField, $appPrefs);
    }


    public function testAppEmpty()
    {
        $httpField  = 'text/html,application/xml;q=0.75';
        $appPrefs   = '';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'html', new QualityFactor(1)),
            new AbsentMimeType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testAppEmptyOutOfOrder()
    {
        $httpField  = 'application/xml;q=0.75, text/html';
        $appPrefs   = '';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'html', new QualityFactor(1)),
            new AbsentMimeType(new QualityFactor(0))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserEmpty()
    {
        $httpField  = '';
        $appPrefs   = 'application/rdf+xml;q=1,text/n3;q=0.5';

        $expectPair = new SharedTypePair(
            new AbsentMimeType(new QualityFactor(0)),
            new MimeType('application', 'rdf+xml', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserEmptyOutOfOrder()
    {
        $httpField  = '';
        $appPrefs   = 'text/n3;q=0.5,application/rdf+xml;q=1';

        $expectPair = new SharedTypePair(
            new AbsentMimeType(new QualityFactor(0)),
            new MimeType('application', 'rdf+xml', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserEmptyAppSameQuality()
    {
        $httpField  = '';
        $appPrefs   = 'text/n3;q=0.5,text/html;q=0.5';

        $expectPair = new SharedTypePair(
            new AbsentMimeType(new QualityFactor(0)),
            new MimeType('text', 'html', new QualityFactor(0.5))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserAppTypesIdentical()
    {
        $httpField  = 'application/rdf+xml;q=0.6,text/n3;q=0.9';
        $appPrefs   = 'text/n3;q=0.9,application/rdf+xml;q=0.6';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'n3', new QualityFactor(0.9)),
            new MimeType('text', 'n3', new QualityFactor(0.9))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testUserAppQualityFactorIntersection()
    {
        $httpField  = 'text/html;q=0.6,application/xml;q=0.9,application/rss+xml;q=0.3';
        $appPrefs   = 'application/atom+xml;q=0.8,text/html;q=0.3,application/rss+xml;q=0.5';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'html', new QualityFactor(0.6)),
            new MimeType('text', 'html', new QualityFactor(0.3))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testBasicTypeWildcard()
    {
        $httpField  = 'text/*;q=0.8,application/xml;q=0.9';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $expectPair = new SharedTypePair(
            new MimeWildcardSubType('text', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testBasicSubtypeWildcard()
    {
        $httpField  = 'text/html;q=0.8,application/xml;q=0.9,*/*;q=0.5';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'html', new QualityFactor(0.8)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }


    public function testWildcardCombination()
    {
        $httpField  = '*/*;q=0.1,text/*;q=0.7,text/html;q=0.9';
        $appPrefs   = 'text/html,application/xml;q=0.7,text/n3;q=0.3';

        $expectPair = new SharedTypePair(
            new MimeType('text', 'html', new QualityFactor(0.9)),
            new MimeType('text', 'html', new QualityFactor(1))
        );

        $negotiate = new Negotiate();
        $resultPair = $negotiate->mimeBest($httpField, $appPrefs);

        $this->assertEquals($expectPair, $resultPair);
    }
}
