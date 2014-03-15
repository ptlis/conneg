<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Type\Mime\Interfaces;

/**
 * Interface providing a regex that is able to parse accept-extens fragments of the Accept HTTP field.
 */
interface AcceptExtensRegexProviderInterface
{
    /**
     * Return a regex capable of parsing the accept-extens fragments of the Accept HTTP field.
     *
     * @return string
     */
    public function getAcceptExtensRegex();
}
