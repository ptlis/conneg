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

namespace ptlis\ConNeg\Tokenizer;

/**
 * Simple tokenizer
 */
class FieldTokenizer
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
     * Tokenize the HTTP field for subsequent processing.
     *
     * Note: we don't need to worry about multi-byte characters; HTTP fields must be ISO-8859-1 encoded.
     *
     * @param string $httpField
     * @param bool $mimeField
     *
     * @return string[]
     */
    public function tokenize($httpField, $mimeField = false)
    {
        $quoteSeparators = array('"', "'");
        $tokenList = array();
        $stringAccumulator = '';
        $lastQuote = '';

        // Iterate through field, character-by-character
        for ($i = 0; $i < strlen($httpField); $i++) {
            $chr = substr($httpField, $i, 1);

            switch (true) {

                // We are at the end of a quoted string
                case $chr === $lastQuote:
                    $tokenList[] = $stringAccumulator;
                    $stringAccumulator = '';
                    $lastQuote = '';
                    break;

                // We have found the beginning of a quoted string
                case in_array($chr, $quoteSeparators):
                    $lastQuote = $chr;
                    break;

                // We are already within a quoted string, but not yet at the end
                case strlen($lastQuote):
                    $stringAccumulator .= $chr;
                    break;

                // Separators found, add previously accumulated string & separator to token list
                case $this->isSeparator($chr, $mimeField):
                    if (strlen($stringAccumulator)) {
                        $tokenList[] = $stringAccumulator;
                        $stringAccumulator = '';
                    }

                    $tokenList[] = $chr;
                    break;

                // Simply accumulate characters
                default:
                    $stringAccumulator .= $chr;
                    break;
            }
        }

        // Handle final component
        if (strlen($stringAccumulator)) {
            $tokenList[] = $stringAccumulator;
        }

        return $tokenList;
    }

    /**
     * Returns true if $chr is a valid separator for this field type.
     *
     * @param string $chr
     * @param string $mimeField
     *
     * @return bool
     */
    private function isSeparator($chr, $mimeField)
    {
        return $this->isTypeSeparator($chr)
            || $this->isParamsSeparator($chr)
            || $this->inMimeSeparator($chr, $mimeField)
            || $this->isParamsKvSeparator($chr);
    }

    /**
     * Returns true if $chr is a type separator.
     *
     * @param string $chr
     *
     * @return bool
     */
    private function isTypeSeparator($chr)
    {
        return self::TYPE_SEPARATOR === $chr;
    }

    /**
     * Returns true if $chr is a parameter separator.
     *
     * @param string $chr
     *
     * @return bool
     */
    private function isParamsSeparator($chr)
    {
        return self::PARAMS_SEPARATOR === $chr;
    }

    /**
     * Returns true if $chr is a mime type/subtype separator.
     *
     * @param string $chr
     * @param bool $mimeField
     *
     * @return bool
     */
    private function inMimeSeparator($chr, $mimeField)
    {
        return $mimeField && self::MIME_SEPARATOR === $chr;
    }

    /**
     * Returns true if $chr is a key/value seperator.
     *
     * @param string $chr
     *
     * @return bool
     */
    private function isParamsKvSeparator($chr)
    {
        return self::PARAMS_KV_SEPARATOR === $chr;
    }
}
