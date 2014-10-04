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

namespace ptlis\ConNeg\TypeBuilder;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\MimeAbsentType;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\MimeWildcardSubType;
use ptlis\ConNeg\Type\MimeWildcardType;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Concrete builder for Mime type.
 */
class MimeTypeBuilder extends TypeBuilder
{
    /**
     * Get the built type from the specification previously provided.
     *
     * @throws InvalidTypeException
     *
     * @return TypeInterface
     */
    public function get()
    {
        switch (true) {
            // Absent Type
            case 0 === strlen($this->type):
                $type = new MimeAbsentType($this->qFactorFactory->get(0, $this->appType));
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
            case $mimeType === '*':
                $type = new MimeWildcardType($this->qFactor);
                break;

            // Wildcard subtype
            case $subType === '*':
                $type = new MimeWildcardSubType($mimeType, $this->qFactor);
                break;

            default:
                $type = new MimeType($mimeType, $subType, $this->qFactor);
                break;
        }

        return $type;
    }
}
