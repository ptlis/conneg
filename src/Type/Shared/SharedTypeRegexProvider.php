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

namespace ptlis\ConNeg\Type\Shared;

use ptlis\ConNeg\Type\Shared\Interfaces\TypeRegexProviderInterface;

/**
 * Provider of generic regex used to parse Accept-Charset, Accept-Encoding & Accept-Language HTTP fields.
 */
class SharedTypeRegexProvider implements TypeRegexProviderInterface
{
    /**
     * Return a regex to parse the required HTTP field.
     *
     * @return string
     */
    public function getTypeRegex()
    {
        return "
            /
                (?<type>[_a-z0-9\-\+\*\.\:]+)                   # Match types
                \s*;?\s*                                        # Quality factor separator
                q?=?(?<qfactor>[0-9]*\.?[0-9]*)?           # Match quality factor
                ,*                                              # Media-range separator
            /ix
        ";
    }
}
