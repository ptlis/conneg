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

use ptlis\ConNeg\Preference\Matched\MatchedPreferencesCollection;
use ptlis\ConNeg\Preference\Matched\MatchedPreferencesSort;
use ptlis\ConNeg\Preference\Preference;
use ptlis\ConNeg\Preference\Matched\MatchedPreferences;


class MatchedPreferencesCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $expectList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            )
        );

        $expectCollection = new MatchedPreferencesCollection($pairSort, $expectList);

        $ascendingCollection = $expectCollection->getAscending();
        foreach ($ascendingCollection as $typePair) {
            $this->assertInstanceOf('ptlis\ConNeg\Preference\Matched\MatchedPreferences', $typePair);
        }
    }


    public function testGetAscending()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $expectList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            )
        );

        $expectCollection = new MatchedPreferencesCollection($pairSort, $expectList);

        $expectServerType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=1'
        );
        $expectClientType = array(
            'iso-8859-5;q=0.8',
            'utf-8;q=0.9'
        );

        $newCollection = $expectCollection->getAscending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetDescending()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            )
        );
        $collection = new MatchedPreferencesCollection($pairSort, $pairList);

        $expectServerType = array(
            'utf-8;q=1',
            'iso-8859-5;q=0.8'
        );
        $expectClientType = array(
            'utf-8;q=0.9',
            'iso-8859-5;q=0.8'
        );

        $newCollection = $collection->getDescending();

        $i = 0;
        foreach ($newCollection as $typePair) {
            $this->assertSame($expectServerType[$i], $typePair->getServerPreference()->__toString());
            $this->assertSame($expectClientType[$i], $typePair->getClientPreference()->__toString());
            $i++;
        }
    }


    public function testGetBest()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            )
        );
        $collection = new MatchedPreferencesCollection($pairSort, $pairList);

        $expectPair = new MatchedPreferences(
            new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
            new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
        );

        $bestPair = $collection->getBest();

        $this->assertEquals($expectPair, $bestPair);
    }


    public function testClone()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            )
        );
        $collection = new MatchedPreferencesCollection($pairSort, $pairList);

        $expectCollection = clone $collection;

        $this->assertEquals($expectCollection, $collection);
        $this->assertNotSame($expectCollection, $collection);
    }


    public function testToString()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            )
        );
        $collection = new MatchedPreferencesCollection($pairSort, $pairList);

        $this->assertEquals('iso-8859-5;q=0.64,utf-8;q=0.9', $collection->__toString());
    }


    public function testCount()
    {
        $pairSort = new MatchedPreferencesSort(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE),
                new Preference(Preference::LANGUAGE, '', 0, Preference::ABSENT_TYPE)
            )
        );

        $pairList = array(
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'iso-8859-5', 0.8, Preference::COMPLETE)
            ),
            new MatchedPreferences(
                new Preference(Preference::LANGUAGE, 'utf-8', 0.9, Preference::COMPLETE),
                new Preference(Preference::LANGUAGE, 'utf-8', 1, Preference::COMPLETE)
            )
        );
        $collection = new MatchedPreferencesCollection($pairSort, $pairList);

        $this->assertEquals(2, count($collection));
    }
}
