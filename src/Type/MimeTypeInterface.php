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

namespace ptlis\ConNeg\Type;

use ptlis\ConNeg\Type\Extens\AcceptExtens;

/**
 * Interface for MIME types.
 */
interface MimeTypeInterface extends TypeInterface
{
    /**
     * Returns the type portion of the media range.
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Returns the subtype portion of the media range.
     *
     * @return string
     */
    public function getMimeSubType();

    /**
     * Get an array of accept-extens fragments belonging to this type.
     *
     * @return AcceptExtens[]
     */
    public function getExtens();
}
