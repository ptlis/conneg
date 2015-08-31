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

use ptlis\ConNeg\Exception\InvalidVariantException;
use ptlis\ConNeg\Preference\Preference;

/**
 * MIME preference builder.
 */
class MimePreferenceBuilder extends AbstractPreferenceBuilder
{
    /**
     * @inheritDoc
     */
    protected function validateVariant($variant)
    {
        if ($this->isFromServer && strlen($variant) > 0) {
            $variantParts = explode('/', $variant);

            // Too many/few slashes
            if (2 !== count($variantParts)) {
                throw new InvalidVariantException('"' . $variant . '" is not a valid mime type');
            }

            // Wildcards disallowed in server preferences
            if (in_array('*', $variantParts)) {
                throw new InvalidVariantException('Wildcards are not allowed in server-provided variants.');
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function normalizeVariant($variant)
    {
        $variantParts = explode('/', $variant);

        // Ignore malformed preferences
        if (2 !== count($variantParts)) {
            $variant = '';

        } else {
            // Variants in form of */foo aren't valid
            list($mimeType, $subType) = $variantParts;
            if ('*' === $mimeType && '*' !== $subType) {
                $variant = '';
            }
        }

        return $variant;
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
            $this->variant,
            $this->getQualityFactor(),
            $this->getPrecedence()
        );
    }

    /**
     * Get the variants's quality factor, defaulting to 0 on absent variant.
     *
     * @return float
     */
    private function getQualityFactor()
    {
        $qFactor = 0.0;

        if (2 === count(explode('/', $this->variant))) {
            $qFactor = $this->qFactor;
        }

        return $qFactor;
    }

    /**
     * Determine the precedence from the variant.
     *
     * @return int
     */
    private function getPrecedence()
    {
        $precedence = Preference::ABSENT;

        // A variant is present
        $variantParts = explode('/', $this->variant);
        if (2 === count($variantParts)) {
            list($mimeType, $subType) = $variantParts;

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
