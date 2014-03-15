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

namespace ptlis\ConNeg\Type\Shared\Interfaces;

/**
 * Interface class for regular expression providers.
 */
interface TypeRegexProviderInterface
{
    /**
     * Return a regex to parse the required HTTP field.
     *
     * @return string
     */
    public function getTypeRegex();
}
