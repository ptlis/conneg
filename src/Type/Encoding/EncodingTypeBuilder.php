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

namespace ptlis\ConNeg\Type\Encoding;

use ptlis\ConNeg\Type\Shared\AbstractTypeBuilder;

/**
 * Concrete builder for Encoding type.
 */
class EncodingTypeBuilder extends AbstractTypeBuilder
{
    /**
     * @return EncodingType
     */
    protected function getType()
    {
        return new EncodingType($this->type, $this->qFactorFactory->get($this->qFactor, $this->appType));
    }
}
