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

namespace ptlis\ConNeg\Type\Mime;

use ptlis\ConNeg\Type\Mime\Interfaces\AcceptExtensRegexProviderInterface;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeRegexProviderInterface;

/**
 * Provider of regex used to parse Accept HTTP field.
 */
class MimeTypeRegexProvider implements TypeRegexProviderInterface, AcceptExtensRegexProviderInterface
{
    /**
     * Return a regex to parse the Accept HTTP field.
     *
     * @return string
     */
    public function getTypeRegex()
    {
        return "
            /
                (?<type>                                        # Full type
                    (?<mime_type>[_a-z0-9\-\+\*\.\:]+)          # Mime type
                    \/                                          # Separator
                    (?<sub_type>[_a-z0-9\-\+\*\.\:]+)           # Mimes subtype
                )

                ;?\s*
                (?<extens>                                      # Matching for accept-extens & quality factor
                    (?:
                        (?:[a-z]+)                              # Accept-extens key or quality factor
                        =*                                      # Optional value separator
                        (\")?(?:[0-9a-z\-\+\.\s]+)?\\3          # Value, optionally between quotation marks
                        ?\s*;?\s*                               # accept-params separator
                    )*                                          # Match 0 or greater quality factors or accept-extension
                )
                ,*                                              # media-range separator
            /ix
        ";
    }


    /**
     * Return a regex capable of parsing the accept-extens fragments of the Accept HTTP field.
     *
     * @return string
     */
    public function getAcceptExtensRegex()
    {
        return "
            /
                (?<key>[_a-z0-9\-\+\*\.]+)                      # Accept-extensions key or quality factor
                (=((\")?(?<value>[0-9a-z\-\+\.\s]+)\\2?))?      # Optional Value, optionally between quotation marks
                \s*;?\s*                                        # accept-extens separator
            /ix
        ";
    }
}
