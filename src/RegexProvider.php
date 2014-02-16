<?php

/**
 * Utility class to provide regular expressions used when parsing Accept* fields.
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

namespace ptlis\ConNeg;

use ptlis\ConNeg\Type\Charset\CharsetRegexProviderInterface;
use ptlis\ConNeg\Type\Encoding\EncodingRegexProviderInterface;
use ptlis\ConNeg\Type\Language\LanguageRegexProviderInterface;
use ptlis\ConNeg\Type\Mime\MimeRegexProviderInterface;

/**
 * Utility class to provide regular expressions used when parsing Accept* fields.
 */
class RegexProvider implements
    CharsetRegexProviderInterface,
    EncodingRegexProviderInterface,
    LanguageRegexProviderInterface,
    MimeRegexProviderInterface
{
    /**
     * Regex used to parse Accept-Charset, Accept-Encoding & Accept-Language fields.
     *
     * @var string
     */
    private $typeRegex = "
        /
            (?<type>[_a-z0-9\-\+\*\.\:]+)                   # Match types
            \s*;?\s*                                        # Quality factor separator
            q?=?(?<qfactor>0\.\d{1,5}|1\.0|[01])?           # Match quality factor
            ,*                                              # Media-range separator
        /ix
    ";

    /**
     * Regex used to parse Accept field.
     *
     * @var string
     */
    private $mimeRegex = "
        /
            (?<type>                                        # Full type
                (?<mime_type>[_a-z0-9\-\+\*\.\:]+)          # Mime type
                \/                                          # Separator
                (?<sub_type>[_a-z0-9\-\+\*\.\:]+)           # Mimes subtype
            )

            (?<extens>                                      # Matching for accept-extens & quality factor
                (?:
                    ;?\s*
                    (?:[a-z]+)                              # Accept-extens key or quality factor
                    =*                                      # Optional value separator
                    (\")?(?:[0-9a-z\-\+\.\s]+)?\\3          # Value, optionally between quotation marks
                    ?\s*;?\s*                               # accept-params separator
                )*                                          # Match 0 or greater quality factors or accept-extension
            )
            ,*                                              # media-range separator
        /ix
    ";

    /**
     * Regex used to parse accept-extens fragments for a single type from Accept field.
     *
     * @var string
     */
    private $extensRegex = "
        /
            (?<key>[_a-z0-9\-\+\*\.]+)                      # Accept-extensions key or quality factor
            (=((\")?(?<value>[0-9a-z\-\+\.\s]+)\\2?))?      # Optional Value, optionally between quotation marks
            \s*;?\s*                                        # accept-extens separator
        /ix
    ";


    /**
     * Get the regex to parse an Accept-Charset field.
     *
     * @return string
     */
    public function getCharsetRegex()
    {
        return $this->typeRegex;
    }


    /**
     * Get the regex to parse an Accept-Encoding field.
     *
     * @return string
     */
    public function getEncodingRegex()
    {
        return $this->typeRegex;
    }


    /**
     * Get the regex to parse an Accept-Language field.
     *
     * @return string
     */
    public function getLanguageRegex()
    {
        return $this->typeRegex;
    }


    /**
     * Get the regex to parse an Accept field.
     *
     * @return string
     */
    public function getMimeRegex()
    {
        return $this->mimeRegex;
    }


    /**
     * Get the regex to parse accept-extens & quality factor.
     *
     * @return string
     */
    public function getAcceptExtensRegex()
    {
        return $this->extensRegex;
    }
}
