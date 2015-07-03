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

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Preference\CollectionInterface;
use ptlis\ConNeg\Preference\PreferenceCollection;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface class for negotiators.
 */
interface NegotiatorInterface
{
    /**
     * Return a collection of types sorted by preference.
     *
     * @param PreferenceCollection $userTypeList
     * @param PreferenceCollection $appTypeList
     *
     * @return CollectionInterface
     */
    public function negotiateAll(PreferenceCollection $userTypeList, PreferenceCollection $appTypeList);

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param PreferenceCollection $userTypeList
     * @param PreferenceCollection $appTypeList
     *
     * @return PreferenceInterface
     */
    public function negotiateBest(PreferenceCollection $userTypeList, PreferenceCollection $appTypeList);
}
