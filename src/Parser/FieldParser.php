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

namespace ptlis\ConNeg\Parser;

use ptlis\ConNeg\Exception\InvalidTypeException;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Parser that accepts a tokenized HTTP Accept or Accept-* field and returns an array of Type value objects.
 */
class FieldParser
{
    /**
     * @var PreferenceBuilderInterface Simple builder that can be used to incrementally build & return a type.
     */
    private $typeBuilder;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $typeBuilder
     */
    public function __construct(PreferenceBuilderInterface $typeBuilder)
    {
        $this->typeBuilder = $typeBuilder;
    }

    /**
     * Accepts a tokenized Accept* HTTP field and returns an array of Type value objects.
     *
     * @throws InvalidTypeException
     *
     * @param array<string> $tokenList   An array of types from a User-Agent; needed as we must be more tolerant of
     *                              malformed fields with incoming data than from the application.
     * @param bool $appField        If true the field came from the application & we error on malformed data otherwise
     *                              we suppress errors for user-agent types.
     * @param string $fromField Which field the tokens came from
     *
     * @return PreferenceInterface[]
     */
    public function parse(array $tokenList, $appField, $fromField)
    {
        // Bundle tokens by type.
        $bundleList = $this->bundleTokens($tokenList, Tokens::TYPE_SEPARATOR);

        $typeList = array();
        foreach ($bundleList as $bundle) {
            $typeList[] = $this->parseBundle($bundle, $appField, $fromField);
        }

        return $typeList;
    }

    /**
     * Accepts tokens for a single type and returns the type value object encapsulating that data.
     *
     * @throws InvalidTypeException
     *
     * @param array<string> $tokenBundle
     * @param bool $appField
     * @param string $fromField
     *
     * @return null|PreferenceInterface
     */
    private function parseBundle(array $tokenBundle, $appField, $fromField)
    {
        $type = null;
        try {
            if (PreferenceInterface::MIME === $fromField) {
                $this->validateBundleMimeType($tokenBundle);
                $typeTokenList = array_slice($tokenBundle, 0, 3);
                $paramTokenList = array_slice($tokenBundle, 3);
            } else {
                $typeTokenList = array_slice($tokenBundle, 0, 1);
                $paramTokenList = array_slice($tokenBundle, 1);
            }

            $paramBundleList = $this->bundleTokens($paramTokenList, Tokens::PARAMS_SEPARATOR);
            $this->validateParamBundleList($paramBundleList, $appField);

            $type = $this->createType($typeTokenList, $paramBundleList, $appField, $fromField);

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
     * @param array<string> $typeTokenList
     * @param array<array<string>> $paramBundleList
     * @param bool $appField
     * @param string $fromField
     *
     * @return PreferenceInterface
     */
    private function createType(array $typeTokenList, array $paramBundleList, $appField, $fromField)
    {
        $builder = $this->typeBuilder
            ->setFromField($fromField)
            ->setFromApp($appField)
            ->setType(implode('', $typeTokenList));

        // Look for quality factor, discarding accept-extens
        foreach ($paramBundleList as $paramBundle) {

            // Correct format for quality factor
            if ($this->isQualityFactor($paramBundle)) {
                $builder = $builder->setQualityFactor($paramBundle[2]);
                break;
            }
        }

        return $builder->get();
    }

    /**
     * Returns true if the provided parameter bundle is a quality factor.
     *
     * @param array<string> $paramBundle
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
     * @param array<string> $tokenList
     * @param string $targetToken The token to split the list up by.
     *
     * @return array<array<string>> an array of arrays - the child array contains the tokens for a single type.
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
     * @param array<string> $bundle
     */
    private function validateBundleMimeType(array $bundle)
    {
        if (
            count($bundle) < 3                          // Too few items in bundle
            || Tokens::MIME_SEPARATOR !== $bundle[1]    // Invalid separator
            || Tokens::isSeparator($bundle[0], true)    // Invalid type
            || Tokens::isSeparator($bundle[2], true)    // Invalid subtype
        ) {
            throw new InvalidTypeException(
                '"' . implode('', $bundle) . '" is not a valid mime type'
            );
        }
    }

    /**
     * Checks to see if the parameters are correctly formed, if an anomaly is detected then an exception is thrown.
     *
     * @throws InvalidTypeException
     *
     * @param array<array<string>> $paramBundleList
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
     * @param string[] $paramBundle
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
