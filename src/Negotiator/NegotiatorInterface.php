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

use ptlis\ConNeg\Preference\Matched\CollectionInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface class that negotiators must implement.
 */
interface NegotiatorInterface
{
    /**
     * Return a collection of types sorted by preference.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     * @param string $fromField
     *
     * @return CollectionInterface
     */
    public function negotiateAll(array $userTypeList, array $appTypeList, $fromField);

    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param PreferenceInterface[] $userTypeList
     * @param PreferenceInterface[] $appTypeList
     * @param string $fromField
     *
     * @return PreferenceInterface
     */
    public function negotiateBest(array $userTypeList, array $appTypeList, $fromField);
}
