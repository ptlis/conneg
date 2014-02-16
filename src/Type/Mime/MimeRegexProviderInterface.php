<?php

/**
 * Interface class for regex provider to be used by factory to parse an Accept field.
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

namespace ptlis\ConNeg\Type\Mime;

/**
 * Interface class for regex provider to be used by factory to parse an Accept field.
 */
interface MimeRegexProviderInterface
{
    /**
     * Get the regex to parse an Accept field.
     *
     * @return string
     */
    public function getMimeRegex();


    /**
     * Get the regex to parse accept-extens & quality factor.
     *
     * @return string
     */
    public function getAcceptExtensRegex();
}
