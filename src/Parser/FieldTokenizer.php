<?php

/**
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2015 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Parser;

use ptlis\ConNeg\Preference\Preference;

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
     * @param string $fromField
     *
     * @return array<string>
     */
    public function tokenize($httpField, $fromField)
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
                case Tokens::isSeparator($chr, Preference::MIME === $fromField):
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

        // Remove any padding whitespace from token list
        $tokenList = array_map('trim', $tokenList);

        return $tokenList;
    }
}
