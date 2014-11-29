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
 * Simple tokenizer
 */
class FieldTokenizer
{
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
                        $tokenList[] = trim($stringAccumulator);
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
     * @param bool $mimeField
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
        return Tokens::TYPE_SEPARATOR === $chr;
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
        return Tokens::PARAMS_SEPARATOR === $chr;
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
        return $mimeField && Tokens::MIME_SEPARATOR === $chr;
    }

    /**
     * Returns true if $chr is a key/value separator.
     *
     * @param string $chr
     *
     * @return bool
     */
    private function isParamsKvSeparator($chr)
    {
        return Tokens::PARAMS_KV_SEPARATOR === $chr;
    }
}
