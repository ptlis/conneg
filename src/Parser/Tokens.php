<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Parser;

/**
 * Class constants used for tokenization.
 */
final class Tokens
{
    /**
     * Token that indicates the boundary between variant data
     */
    const VARIANT_SEPARATOR = ',';

    /**
     * Token that indicates the boundary between the variant & any params attached to it.
     */
    const PARAMS_SEPARATOR = ';';

    /**
     * Token that splits the mime type & subtypes.
     */
    const MIME_SEPARATOR = '/';

    /**
     * Token that splits params into keys and values.
     */
    const PARAMS_KV_SEPARATOR = '=';


    /**
     * Return true if the provided string is one of the separator tokens defined in this class.
     *
     * @param string $string
     * @param bool $mimeField
     *
     * @return bool
     */
    public static function isSeparator(string $string, bool $mimeField): bool
    {
        $separatorList = array(
            self::VARIANT_SEPARATOR,
            self::PARAMS_SEPARATOR,
            self::PARAMS_KV_SEPARATOR
        );

        return in_array($string, $separatorList) || ($mimeField && Tokens::MIME_SEPARATOR === $string);
    }
}
