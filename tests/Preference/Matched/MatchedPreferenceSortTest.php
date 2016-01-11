<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Test\Preference\Matched;

use ptlis\ConNeg\Preference\Matched\MatchedPreferenceSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreference;

class MatchedPreferenceSortTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAscendingOne()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.9, Preference::COMPLETE),
            new Preference('en-gb', 1, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.8, Preference::COMPLETE),
            new Preference('fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'fr;q=0.8',
            'en-gb;q=1'
        );

        $expectClientType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }

    public function testGetAscendingTwo()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.9, Preference::COMPLETE),
            new Preference('en-gb', 0.5, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.8, Preference::COMPLETE),
            new Preference('fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'en-gb;q=0.5',
            'fr;q=0.8'
        );

        $expectClientType = array(
            'en-gb;q=0.9',
            'fr;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }



    public function testGetAscendingThree()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.8, Preference::COMPLETE),
            new Preference('en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.8, Preference::COMPLETE),
            new Preference('fr', 0.9, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'fr;q=0.9',
            'en-gb;q=0.9'
        );

        $expectClientType = array(
            'fr;q=0.8',
            'en-gb;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetAscendingFour()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.9, Preference::COMPLETE),
            new Preference('en-gb', 0.8, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.8, Preference::COMPLETE),
            new Preference('fr', 0.9, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'fr;q=0.9',
            'en-gb;q=0.8'
        );

        $expectClientType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetAscendingFive()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.8, Preference::COMPLETE),
            new Preference('en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.9, Preference::COMPLETE),
            new Preference('fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'en-gb;q=0.9',
            'fr;q=0.8'
        );

        $expectClientType = array(
            'en-gb;q=0.8',
            'fr;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetAscendingSix()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('', 0, Preference::ABSENT),
            new Preference('en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('', 0, Preference::ABSENT),
            new Preference('fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $expectClientType = array(
            '',
            ''
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetAscendingSeven()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.9, Preference::COMPLETE),
            new Preference('', 0, Preference::ABSENT)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.8, Preference::COMPLETE),
            new Preference('', 0, Preference::ABSENT)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortAscending($typePairList);

        $expectServerType = array(
            '',
            ''
        );

        $expectClientType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetDescending()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('en-gb', 0.8, Preference::COMPLETE),
            new Preference('en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreference(
            Preference::LANGUAGE,
            new Preference('fr', 0.9, Preference::COMPLETE),
            new Preference('fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferenceSort();

        $newCollection = $sort->sortDescending($typePairList);

        $expectServerType = array(
            'fr;q=0.8',
            'en-gb;q=0.9'
        );

        $expectClientType = array(
            'fr;q=0.9',
            'en-gb;q=0.8'
        );

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }
}
