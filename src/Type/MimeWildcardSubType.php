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

use ptlis\ConNeg\QualityFactor\QualityFactorInterface;

/**
 * Class for MIME with wildcard subtype.
 */
class MimeWildcardSubType extends MimeType
{
    /**
     * Constructor
     *
     * @param string $type
     * @param QualityFactorInterface $qFactor
     */
    public function __construct($type, QualityFactorInterface $qFactor)
    {
        parent::__construct($type, '*', $qFactor);
        $this->precedence = 1;
    }
}
