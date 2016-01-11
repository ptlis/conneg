<?php

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Preference\Builder;

use ptlis\ConNeg\Exception\InvalidVariantException;

/**
 * Shared preference building implementation.
 */
abstract class AbstractPreferenceBuilder implements PreferenceBuilderInterface
{
    /**
     * @var bool
     */
    protected $isFromServer = false;

    /**
     * @var string
     */
    protected $fromField;

    /**
     * @var string
     */
    protected $variant = '';

    /**
     * @var float
     */
    protected $qFactor = 1;


    /**
     * @inheritDoc
     */
    public function setFromServer($isFromServer)
    {
        $clone = clone $this;
        $clone->isFromServer = $isFromServer;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function setFromField($fromField)
    {
        $clone = clone $this;
        $clone->fromField = $fromField;

        return $clone;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidVariantException If the provided variant is not valid.
     */
    public function setVariant($variant)
    {
        if ($this->isFromServer) {
            $this->validateVariant($variant);
        }

        $clone = clone $this;
        $clone->variant = $this->normalizeVariant($variant);

        return $clone;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidVariantException If an invalid quality factor in encountered when building an server preference.
     */
    public function setQualityFactor($qFactor)
    {
        if ($this->isFromServer && !$this->validQualityFactor($qFactor)) {
            throw new InvalidVariantException('Invalid quality factor "' . $qFactor . '" in server preferences');
        }

        $qFactor = $this->normalizeQualityFactor($qFactor);

        $clone = clone $this;
        $clone->qFactor = $qFactor;

        return $clone;
    }

    /**
     * Validate the variant, returning true if the variant is valid.
     *
     * @throws InvalidVariantException If the provided variant is not valid.
     *
     * @param string $variant
     */
    abstract protected function validateVariant($variant);

    /**
     * Normalises the variant.
     *
     * @param string $variant
     *
     * @return string
     */
    protected function normalizeVariant($variant)
    {
        return $variant;
    }

    /**
     * Validate the quality factor, returning true if the quality factor is valid.
     *
     * @param float $qFactor
     *
     * @return bool
     */
    private function validQualityFactor($qFactor)
    {
        return is_numeric($qFactor) && $qFactor >= 0 && $qFactor <= 1;
    }

    /**
     * Normalises the provided quality factor, ensuring that the value returned is a float between 0 and 1 (inclusive).
     *
     * @param float $qFactor
     *
     * @return float
     */
    private function normalizeQualityFactor($qFactor)
    {
        if (!is_numeric($qFactor)) {
            $qFactor = 1.0;

        } elseif ($qFactor < 0) {
            $qFactor = 0.0;

        } elseif ($qFactor > 1) {
            $qFactor = 1.0;

        } else {
            $qFactor = floatval($qFactor);
        }

        return $qFactor;
    }
}
