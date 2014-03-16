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

namespace ptlis\ConNeg\Type\Mime;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\Shared\AbstractTypeBuilder;
use ptlis\ConNeg\Type\Shared\Interfaces\TypeInterface;

/**
 * Concrete builder for Mime type.
 */
class MimeTypeBuilder extends AbstractTypeBuilder
{
    public function get()
    {
        switch (true) {
            // Absent Type
            case 0 === strlen($this->type):
                $type = new AbsentMimeType($this->qFactorFactory->get(0, $this->appType));
                break;

            // Type & subtype present
            case 2 === count($explodedType = explode('/', $this->type)):
                list($mimeType, $subType) = $explodedType;

                $this->validateType($mimeType, $subType);

                $type = $this->getType();
                break;

            default:
                throw new InvalidTypeException(
                    '"' . $this->type . '" is not a valid mime type'
                );
                break;
        }

        return $type;
    }


    /**
     * Validate the provided type data.
     *
     * @throws InvalidTypeException
     *
     * @param string $mimeType
     * @param string $subType
     */
    private function validateType($mimeType, $subType)
    {
        if (($mimeType === '*' || $subType === '*') && $this->appType) {
            throw new InvalidTypeException(
                'Wildcards are not valid in application-provided types.'
            );
        }

        if ($mimeType === '*' && $subType !== '*') {
            throw new InvalidTypeException(
                '"' . $this->type . '" is not a valid mime type'
            );
        }
    }


    /**
     * Get the type object from the provided specification.
     *
     * @throws InvalidTypeException
     *
     * @return TypeInterface
     */
    protected function getType()
    {
        $explodedType = explode('/', $this->type);
        list($mimeType, $subType) = $explodedType;

        switch (true) {
            // Full wildcard type
            case $mimeType === '*' && $subType === '*':
                $type = new MimeWildcardType($this->qFactorFactory->get($this->qFactor, $this->appType));
                break;

            // Wildcard subtype
            case $mimeType !== '*' && $subType === '*':
                $type = new MimeWildcardSubType($mimeType, $this->qFactorFactory->get($this->qFactor, $this->appType));
                break;

            default:
                $type = new MimeType($mimeType, $subType, $this->qFactorFactory->get($this->qFactor, $this->appType));
                break;
        }

        return $type;
    }
}
