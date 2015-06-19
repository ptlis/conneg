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

namespace ptlis\ConNeg\TypeBuilder;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\MimeType;
use ptlis\ConNeg\Type\MimeTypeInterface;

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
     * @return MimeTypeInterface
     */
    public function get()
    {
        switch (true) {
            // Absent Type
            case 0 === strlen($this->type):
                $type = new MimeType('', '', 0, MimeType::ABSENT_TYPE);
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
        }

        $this->setDefaults();

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
        if ($this->appType) {
            $this->noAppWildcards($mimeType, $subType);
        } else {
            $this->validUserWildcard($mimeType, $subType);
        }
    }

    /**
     * Ensure that application-provided types do not include wildcards.
     *
     * @throws InvalidTypeException
     *
     * @param string $mimeType
     * @param string $subType
     */
    private function noAppWildcards($mimeType, $subType)
    {
        if (($mimeType === '*' || $subType === '*')) {
            throw new InvalidTypeException(
                'Wildcards are not valid in application-provided types.'
            );
        }
    }

    /**
     * Ensure that user-agent provided wildcards are valid (you may not have a wildcard type but a concrete subtype).
     *
     * @throws InvalidTypeException
     *
     * @param string $mimeType
     * @param string $subType
     */
    private function validUserWildcard($mimeType, $subType)
    {
        if ($mimeType === '*' && $subType !== '*') {
            throw new InvalidTypeException(
                '"' . $this->type . '" is not a valid mime type.'
            );
        }
    }

    /**
     * Get the type object from the provided specification.
     *
     * @throws InvalidTypeException
     *
     * @return MimeTypeInterface
     */
    protected function getType()
    {
        $explodedType = explode('/', $this->type);
        list($mimeType, $subType) = $explodedType;

        $precedence = MimeType::EXACT_TYPE;

        if ('*' === $mimeType) {
            $precedence = MimeType::WILDCARD_TYPE;
        } elseif ('*' === $subType) {
            $precedence = MimeType::WILDCARD_SUBTYPE;
        }

        return new MimeType($mimeType, $subType, $this->qFactor, $precedence, $this->acceptExtensList);
    }
}
