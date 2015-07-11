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
 * Shared type builder (handles Charset, Encoding & Language).
 */
class PreferenceBuilder extends AbstractPreferenceBuilder
{
    /**
     * @inheritDoc
     */
    protected function validateType($type)
    {
        if ($this->isFromApp && '*' === $type) {
            throw new InvalidTypeException('Wildcards are not allowed in application-provided types.');
        }
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

        $precedence = Preference::COMPLETE;
        $qFactor = $this->qFactor;

        if ('*' === $this->type) {
            $precedence = Preference::WILDCARD;

        } elseif (!strlen($this->type)) {
            $precedence = Preference::ABSENT_TYPE;
            $qFactor = 0;

        // Special handling for Accept-Language field
        } elseif (Preference::LANGUAGE === $this->fromField && '-*' === substr($this->type, -2, 2)) {
            $precedence = Preference::PARTIAL_WILDCARD;
        }

        return new Preference(
            $this->fromField,
            $this->type,
            $qFactor,
            $precedence
        );
    }
}
