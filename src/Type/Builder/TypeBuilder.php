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

namespace ptlis\ConNeg\Type\Builder;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\Type;

/**
 * Shared type builder (handles Charset, Encoding & Language).
 */
class TypeBuilder extends AbstractTypeBuilder
{
    /**
     * {@inheritDoc}
     */
    protected function validateType($type)
    {
        if ($this->isFromApp && '*' === $type) {
            throw new InvalidTypeException('Wildcards are not allowed in application-provided types.');
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return Type
     */
    public function get()
    {
        $precedence = Type::EXACT_TYPE;
        $qFactor = $this->qFactor;

        if ('*' === $this->type) {
            $precedence = Type::WILDCARD_TYPE;

        } elseif (!strlen($this->type)) {
            $precedence = Type::ABSENT_TYPE;
            $qFactor = 0;

        // TODO: Only for Accept-Language field
        } elseif ('-*' === substr($this->type, -2, 2)) {
            $precedence = Type::WILDCARD_PARTIAL_LANG;
        }

        return new Type(
            $this->type,
            $qFactor,
            $precedence
        );
    }
}
