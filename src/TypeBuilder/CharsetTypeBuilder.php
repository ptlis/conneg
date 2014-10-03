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

namespace ptlis\ConNeg\TypeBuilder;

use ptlis\ConNeg\Type\CharsetType;

/**
 * Concrete builder for Charset type.
 */
class CharsetTypeBuilder extends AbstractTypeBuilder
{
    /**
     * @return CharsetType
     */
    protected function getType()
    {
        return new CharsetType($this->type, $this->qFactor);
    }
}