<?php

/**
 * Interface class for regex provider to be used by factory to parse an Accept-Charset field.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Type\Charset;

/**
 * Interface class for regex provider to be used by factory to parse an Accept-Charset field.
 */
interface CharsetRegexProviderInterface
{
    /**
     * Get the regex to parse an Accept-Charset field.
     *
     * @return string
     */
    public function getCharsetRegex();
}