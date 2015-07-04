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

namespace ptlis\ConNeg\Matcher;

use ptlis\ConNeg\Preference\CollectionInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface class that matching engines must implement.
 */
interface MatcherInterface
{
    /**
     * Return a collection of types sorted by preference.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     *
     * @return CollectionInterface
     */
    public function negotiateAll(array $userTypeList, array $appTypeList);

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     *
     * @return PreferenceInterface
     */
    public function negotiateBest(array $userTypeList, array $appTypeList);
}
