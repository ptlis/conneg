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

namespace ptlis\ConNeg\Parse;

use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\TypeBuilder\TypeBuilderInterface;

/**
 * Parser that accepts a tokenized HTTP Accept or Accept-* field and returns an array of Type value objects.
 */
class FieldParser
{
    /**
     * @var TypeBuilderInterface Simple builder that can be used to incrementally build & return a type.
     */
    private $typeBuilder;

    /**
     * @var bool When true then we are parsing the Accept field and need to apply some special rules
     */
    private $mimeField;


    /**
     * Constructor.
     *
     * @param TypeBuilderInterface $typeBuilder
     * @param bool $mimeField
     */
    public function __construct(TypeBuilderInterface $typeBuilder, $mimeField)
    {
        $this->typeBuilder = $typeBuilder;
        $this->mimeField = $mimeField;
    }

    /**
     * Accepts a tokenized Accept* HTTP field and returns an array of Type value objects.
     *
     * @throws InvalidTypeException
     *
     * @param string[] $tokenList   An array of types from a User-Agent; needed as we must be more tolerant of malformed
     *                              fields with incoming data than from the application.
     * @param bool $appField        If true the field came from the application & we error on malformed data otherwise
     *                              we suppress errors for user-agent types.
     *
     * @return TypeCollection
     */
    public function parse(array $tokenList, $appField)
    {
        // Bundle tokens by type.
        $bundleList = $this->bundleTokens($tokenList, Tokens::TYPE_SEPARATOR);

        $typeList = array();
        foreach ($bundleList as $bundle) {
            $typeList[] = $this->parseBundle($bundle, $appField);
        }

        $collection = new TypeCollection();
        $collection->setList($typeList);

        return $collection;
    }

    /**
     * Accepts tokens for a single type and returns the type value object encapsulating that data.
     *
     * @throws InvalidTypeException
     *
     * @param string[] $tokenBundle
     * @param bool $appField
     *
     * @return null|TypeInterface
     */
    private function parseBundle(array $tokenBundle, $appField)
    {
        $type = null;
        try {
            if ($this->mimeField) {
                $this->validateBundleMimeType($tokenBundle);
                $typeTokenList = array_slice($tokenBundle, 0, 3);
                $paramTokenList = array_slice($tokenBundle, 3);
            } else {
                $typeTokenList = array_slice($tokenBundle, 0, 1);
                $paramTokenList = array_slice($tokenBundle, 1);
            }

            $paramBundleList = $this->bundleTokens($paramTokenList, Tokens::PARAMS_SEPARATOR);
            $this->validateParamBundleList($paramBundleList, $appField);

            $type = $this->createType($typeTokenList, $paramBundleList, $appField);

        } catch (InvalidTypeException $e) {
            if ($appField) {
                throw $e;
            }
        }

        return $type;
    }

    /**
     * Accepts the bundled tokens for type & parameter data and builds the Type value object.
     *
     * @param string[] $typeTokenList
     * @param string[] $paramBundleList
     * @param bool $appField
     *
     * @return TypeInterface
     */
    private function createType(array $typeTokenList, array $paramBundleList, $appField)
    {
        $this->typeBuilder
            ->setAppType($appField)
            ->setType(implode('', $typeTokenList));

        // Look for quality factor
        foreach ($paramBundleList as $paramBundle) {

            // Correct format for quality factor
            if ($this->isQualityFactor($paramBundle)) {
                $this->typeBuilder->setQualityFactor($paramBundle[2]);
            }
        }

        return $this->typeBuilder->get();
    }

    /**
     * Returns true if the provided parameter bundle is a quality factor.
     *
     * @param string[] $paramBundle
     *
     * @return bool
     */
    private function isQualityFactor(array $paramBundle)
    {
        return 3 == count($paramBundle)
            && 'q' === $paramBundle[0]
            && Tokens::PARAMS_KV_SEPARATOR === $paramBundle[1];
    }

    /**
     * Splits token list up into one bundle per type for later processing.
     *
     * @param string[] $tokenList
     * @param string $targetToken The token to split the list up by.
     *
     * @return string[] an array of arrays - the child array contains the tokens for a single type.
     */
    private function bundleTokens(array $tokenList, $targetToken)
    {
        $bundleList = array();
        $bundle = array();

        foreach ($tokenList as $token) {

            // Collect tokens
            if ($targetToken !== $token) {
                $bundle[] = $token;

            // On type separator add bundle to list & re-initialize empty bundle
            } elseif (count($bundle)) {
                $bundleList[] = $bundle;
                $bundle = array();
            }
        }

        // Handle trailing type
        if (count($bundle)) {
            $bundleList[] = $bundle;
        }

        return $bundleList;
    }

    /**
     * Checks to see if the bundle is valid for a mime type, if an anomaly is detected then an exception is thrown.
     *
     * @throws InvalidTypeException
     *
     * @param string[] $bundle
     */
    private function validateBundleMimeType(array $bundle)
    {
        switch (true) {
            case count($bundle) < 3:                        // Too few items in bundle
            case Tokens::MIME_SEPARATOR !== $bundle[1]:     // Invalid separator
            case Tokens::isToken($bundle[0]):               // Invalid type
            case Tokens::isToken($bundle[2]):               // Invalid subtype
                throw new InvalidTypeException(
                    '"' . implode('', $bundle) . '" is not a valid mime type'
                );
                break;
            default:
                // All is well
                break;
        };
    }

    /**
     * Checks to see if the parameters are correctly formed, if an anomaly is detected then an exception is thrown.
     *
     * @throws InvalidTypeException
     *
     * @param string[] $paramBundleList
     * @param bool $appField        If true the field came from the application & we error on malformed data otherwise
     *                              we suppress errors for user-agent types.
     */
    private function validateParamBundleList(array $paramBundleList, $appField)
    {
        foreach ($paramBundleList as $paramBundle) {

            try {
                $this->validateParamBundle($paramBundle);
            } catch (InvalidTypeException $e) {

                // Rethrow exception only if the field was provided by the application
                if ($appField) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Validate a single parameter bundle.
     *
     * Due to the way we process tokens the only required should be for the correct number of tokens.
     *
     * @throws InvalidTypeException
     *
     * @param array $paramBundle
     */
    private function validateParamBundle(array $paramBundle)
    {
        // Wrong number of components
        if (1 !== count($paramBundle) && 3 !== count($paramBundle)) {
            throw new InvalidTypeException(
                'Invalid count for parameters; expecting 1 or 3, got "' . count($paramBundle) . '"'
            );
        }
    }
}
