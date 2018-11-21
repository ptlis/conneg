<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference;

/**
 * Interface for value types storing variant preferences.
 */
interface PreferenceInterface
{
    /** Null/absent variant, used as a placeholder for matched preferences. */
    const ABSENT = -1;

    /** Wildcard match */
    const WILDCARD = 0;

    /** Partial wildcard (e.g. text/* or en-*) */
    const PARTIAL_WILDCARD = 1;

    /** Fully qualified variant */
    const COMPLETE = 2;


    /** Charset variant */
    const CHARSET = 'Accept-Charset';

    /** Encoding variant */
    const ENCODING = 'Accept-Encoding';

    /** Language variant */
    const LANGUAGE = 'Accept-Language';

    /** Mime variant */
    const MIME = 'Accept';


    /**
     * Return the variant name.
     *
     * @return string
     */
    public function getVariant();

    /**
     * Return the precedence of the variant (wildcards are superseded by full matches etc).
     *
     * @return int
     */
    public function getPrecedence();

    /**
     * Returns the quality factor for the variant.
     *
     * @return float
     */
    public function getQualityFactor();

    /**
     * Create string representation of the preference.
     *
     * @return string
     */
    public function __toString();
}
