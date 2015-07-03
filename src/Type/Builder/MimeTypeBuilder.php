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
 * MIME type builder.
 */
class MimeTypeBuilder extends AbstractTypeBuilder
{
    /**
     * {@inheritDoc}
     */
    protected function validateType($type)
    {
        if ($this->isFromApp && strlen($type) > 0) {
            $typeParts = explode('/', $type);

            // Too many/few slashes
            if (2 !== count($typeParts)) {
                throw new InvalidTypeException('"' . $type . '" is not a valid mime type');
            }

            // Wildcards disallowed in app types
            if (in_array('*', $typeParts)) {
                throw new InvalidTypeException('Wildcards are not allowed in application-provided types.');
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function normalizeType($type)
    {
        $typeParts = explode('/', $type);

        // Ignore misformatted types
        if (2 !== count($typeParts)) {
            $type = '';

        } else {

            // Types in form of */foo aren't valid
            list($mimeType, $subType) = $typeParts;
            if ('*' === $mimeType && '*' !== $subType) {
                $type = '';
            }
        }

        return $type;
    }

    /**
     * {@inheritDoc}
     *
     * @return Type
     */
    public function get()
    {
        // Defaults for absent type
        $precedence = Type::ABSENT_TYPE;
        $qFactor = 0;

        // A type was present
        $explodedType = explode('/', $this->type);
        if (2 === count($explodedType)) {

            list($mimeType, $subType) = $explodedType;

            $precedence = Type::EXACT_TYPE;
            $qFactor = $this->qFactor;

            if ('*' === $mimeType) {
                $precedence = Type::WILDCARD_TYPE;

            } elseif ('*' === $subType) {
                $precedence = Type::WILDCARD_SUBTYPE;
            }
        }

        return new Type(
            $this->type,
            $qFactor,
            $precedence
        );
    }
}
