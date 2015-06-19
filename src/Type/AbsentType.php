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

namespace ptlis\ConNeg\Type;

/**
 * Class for representing the absence of a type.
 */
class AbsentType extends Type
{
    /**
     * Constructor
     *
     * @param float $qFactor
     */
    public function __construct($qFactor)
    {
        parent::__construct('', $qFactor);
        $this->precedence = -1;
    }

    /**
     * Returns an empty string.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
