<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Matched;

use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Interface for preferences.
 */
interface MatchedPreferenceInterface extends PreferenceInterface
{
    /**
     * Returns the client's preference.
     *
     * @return PreferenceInterface
     */
    public function getClientPreference(): PreferenceInterface;

    /**
     * Returns the server's preference.
     *
     * @return PreferenceInterface
     */
    public function getServerPreference(): PreferenceInterface;
}
