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

namespace ptlis\ConNeg\Preference\Builder;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Preference\Preference;

/**
 * MIME preference builder.
 */
class MimePreferenceBuilder extends AbstractPreferenceBuilder
{
    /**
     * @inheritDoc
     */
    protected function validateType($type)
    {
        if ($this->isFromServer && strlen($type) > 0) {
            $typeParts = explode('/', $type);

            // Too many/few slashes
            if (2 !== count($typeParts)) {
                throw new InvalidTypeException('"' . $type . '" is not a valid mime type');
            }

            // Wildcards disallowed in server preferences
            if (in_array('*', $typeParts)) {
                throw new InvalidTypeException('Wildcards are not allowed in server-provided types.');
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function normalizeType($type)
    {
        $typeParts = explode('/', $type);

        // Ignore malformed preferences
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
     * @inheritDoc
     *
     * @throws \RuntimeException if the HTTP field was not provided
     */
    public function get()
    {
        if (is_null($this->fromField)) {
            throw new \RuntimeException(
                'The HTTP field must be provided to the builder.'
            );
        }

        return new Preference(
            $this->fromField,
            $this->type,
            $this->getQualityFactor(),
            $this->getPrecedence()
        );
    }

    /**
     * Get the type's quality factor, defaulting to 0 on absent types.
     *
     * @return float
     */
    private function getQualityFactor()
    {
        $qFactor = 0.0;

        if (2 === count(explode('/', $this->type))) {
            $qFactor = $this->qFactor;
        }

        return $qFactor;
    }

    /**
     * Determine the precedence from the type.
     *
     * @return int
     */
    private function getPrecedence()
    {
        $precedence = Preference::ABSENT_TYPE;

        // A type is present
        $explodedType = explode('/', $this->type);
        if (2 === count($explodedType)) {
            list($mimeType, $subType) = $explodedType;

            $precedence = Preference::COMPLETE;

            if ('*' === $mimeType) {
                $precedence = Preference::WILDCARD;

            } elseif ('*' === $subType) {
                $precedence = Preference::PARTIAL_WILDCARD;
            }
        }

        return $precedence;
    }
}
