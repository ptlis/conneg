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

namespace ptlis\ConNeg\Test\Preference\Matched;

use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;

class MatchedPreferencesSortTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAscendingOne()
    {
        $typePairList = array();
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 1, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.5, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.8, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

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


    public function testGetBestEmpty()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $best = $sort->getBest(
            array(),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $expect = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
            new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
        );

        $this->assertEquals($expect, $best);
    }


    public function testGetBest()
    {
        $sort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $typePairList = array();
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'en-gb', 0.8, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'en-gb', 0.9, Preference::COMPLETE)
        );
        $typePairList[] = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $best = $sort->getBest(
            $typePairList,
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $expect = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'fr', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'fr', 0.8, Preference::COMPLETE)
        );

        $this->assertEquals($expect, $best);
    }
}
