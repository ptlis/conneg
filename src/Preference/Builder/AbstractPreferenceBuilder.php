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

/**
 * Shared preference building implementation.
 */
abstract class AbstractPreferenceBuilder implements PreferenceBuilderInterface
{
    /**
     * @var bool
     */
    protected $isFromApp = false;

    /**
     * @var string
     */
    protected $fromField;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var float
     */
    protected $qFactor = 1;


    /**
     * @inheritDoc
     */
    public function setFromApp($isFromApp)
    {
        $clone = clone $this;
        $clone->isFromApp = $isFromApp;

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
     * @throws InvalidTypeException If the provided type is not valid.
     */
    public function setType($type)
    {
        if ($this->isFromApp) {
            $this->validateType($type);
        }

        $clone = clone $this;
        $clone->type = $this->normalizeType($type);

        return $clone;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidTypeException If an invalid quality factor in encountered when building an application type.
     */
    public function setQualityFactor($qFactor)
    {
        if ($this->isFromApp && !$this->validQualityFactor($qFactor)) {
            throw new InvalidTypeException('Invalid quality factor "' . $qFactor . '" in application preferences');
        }

        $qFactor = $this->normalizeQualityFactor($qFactor);

        $clone = clone $this;
        $clone->qFactor = $qFactor;

        return $clone;
    }

    /**
     * Validate the type, returning true if the type is valid.
     *
     * @throws InvalidTypeException If the provided type is not valid.
     *
     * @param string $type
     */
    abstract protected function validateType($type);

    /**
     * Normalises the type.
     *
     * @param string $type
     *
     * @return string
     */
    protected function normalizeType($type)
    {
        return $type;
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
