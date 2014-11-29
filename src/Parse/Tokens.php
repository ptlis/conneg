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

namespace ptlis\ConNeg\Parse;

/**
 * Class constants used for tokenization.
 */
class Tokens
{
    /**
     * Token that indicates the boundary between type data
     */
    const TYPE_SEPARATOR = ',';

    /**
     * Token that indicates the boundary between the type & any params attached to it.
     */
    const PARAMS_SEPARATOR = ';';

    /**
     * Token that splits the mime type & subtypes.
     */
    const MIME_SEPARATOR = '/';

    /**
     * Token that splits params into keys and values (where applicable)
     */
    const PARAMS_KV_SEPARATOR = '=';


    /**
     * Return true if the provided string is one of the separator tokens defined in this class.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isToken($string)
    {
        $isToken = false;

        switch ($string) {
            case self::TYPE_SEPARATOR:
            case self::PARAMS_SEPARATOR:
            case self::MIME_SEPARATOR:
            case self::PARAMS_KV_SEPARATOR:
                $isToken = true;
                break;
        }

        return $isToken;
    }
}