<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Builder;

use ptlis\ConNeg\Exception\InvalidVariantException;
use ptlis\ConNeg\Preference\Preference;

/**
 * Shared preference builder (handles Charset, Encoding & Language).
 */
class PreferenceBuilder extends AbstractPreferenceBuilder
{
    /**
     * @inheritDoc
     */
    protected function validateVariant(string $variant)
    {
        if ($this->isFromServer && '*' === $variant) {
            throw new InvalidVariantException('Wildcards are not allowed in server-provided variants.');
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

        return new Preference(
            $this->variant,
            $this->getQualityFactor(),
            $this->getPrecedence()
        );
    }

    /**
     * Get the variant's quality factor, defaulting to 0 on absent variant.
     *
     * @return float
     */
    private function getQualityFactor()
    {
        $qFactor = 0.0;

        if (strlen($this->variant)) {
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

        // Wildcards
        if ('*' === $this->variant) {
            $precedence = Preference::WILDCARD;

        // Special handling for Accept-Language field
        } elseif (Preference::LANGUAGE === $this->fromField && '-*' === substr($this->variant, -2, 2)) {
            $precedence = Preference::PARTIAL_WILDCARD;

        // Full match
        } elseif (strlen($this->variant)) {
            $precedence = Preference::COMPLETE;
        }

        return $precedence;
    }
}
