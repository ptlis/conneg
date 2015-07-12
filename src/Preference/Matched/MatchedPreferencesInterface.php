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

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface for types.
 */
interface MatchedPreferencesInterface extends PreferenceInterface
{
    /**
     * Returns the user's preference.
     *
     * @return PreferenceInterface
     */
    public function getUserPreference();

    /**
     * Returns the application's preference.
     *
     * @return PreferenceInterface
     */
    public function getAppPreference();
}
