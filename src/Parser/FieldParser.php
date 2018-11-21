<?php declare(strict_types = 1);

/**
 * @copyright   (c) 2006-present brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace ptlis\ConNeg\Parser;

use ptlis\ConNeg\Exception\InvalidVariantException;
use ptlis\ConNeg\Preference\Builder\PreferenceBuilderInterface;
use ptlis\ConNeg\Preference\PreferenceInterface;

/**
 * Parser that accepts a tokenized HTTP Accept or Accept-* field and returns an array of Preference value objects.
 */
class FieldParser
{
    /**
     * @var PreferenceBuilderInterface
     */
    private $prefBuilder;

    /**
     * @var PreferenceBuilderInterface
     */
    private $mimePrefBuilder;


    /**
     * Constructor.
     *
     * @param PreferenceBuilderInterface $prefBuilder
     * @param PreferenceBuilderInterface $mimePrefBuilder
     */
    public function __construct(
        PreferenceBuilderInterface $prefBuilder,
        PreferenceBuilderInterface $mimePrefBuilder
    ) {
        $this->prefBuilder = $prefBuilder;
        $this->mimePrefBuilder = $mimePrefBuilder;
    }

    /**
     * Accepts a tokenized Accept* HTTP field and returns an array of Preference value objects.
     *
     * @throws InvalidVariantException
     *
     * @param array<string> $tokenList Parsed tokens
     * @param bool $serverField If true the field came from the server & we error on malformed data, otherwise we
     *                          suppress errors for client preferences..
     * @param string $fromField Which field the tokens came from
     *
     * @return PreferenceInterface[]
     */
    public function parse(array $tokenList, $serverField, $fromField)
    {
        // Bundle tokens by variant.
        $bundleList = $this->bundleTokens($tokenList, Tokens::VARIANT_SEPARATOR);

        $prefList = array();
        foreach ($bundleList as $bundle) {
            $prefList[] = $this->parseBundle($bundle, $serverField, $fromField);
        }

        return $prefList;
    }

    /**
     * Accepts tokens for a single variant and returns the Preference value object encapsulating that data.
     *
     * @throws InvalidVariantException
     *
     * @param array<string> $tokenBundle
     * @param bool $serverField
     * @param string $fromField
     *
     * @return null|PreferenceInterface
     */
    private function parseBundle(array $tokenBundle, $serverField, $fromField)
    {
        $pref = null;
        try {
            list($variantTokenList, $paramTokenList) = $this->splitVariantAndParamTokens($tokenBundle, $fromField);

            $paramBundleList = $this->bundleTokens($paramTokenList, Tokens::PARAMS_SEPARATOR);
            $this->validateParamBundleList($paramBundleList, $serverField);

            $pref = $this->createPreference($variantTokenList, $paramBundleList, $serverField, $fromField);

        } catch (InvalidVariantException $e) {
            if ($serverField) {
                throw $e;
            }
        }

        return $pref;
    }

    /**
     * Splits the token list into variant & parameter arrays.
     *
     * @throws InvalidVariantException
     *
     * @param array<string> $tokenBundle
     * @param string $fromField
     *
     * @return string[][]
     */
    private function splitVariantAndParamTokens(array $tokenBundle, $fromField)
    {
        if (PreferenceInterface::MIME === $fromField) {
            $this->validateBundleMimeVariant($tokenBundle);
            $variantTokenList = array_slice($tokenBundle, 0, 3);
            $paramTokenList = array_slice($tokenBundle, 3);
        } else {
            $variantTokenList = array_slice($tokenBundle, 0, 1);
            $paramTokenList = array_slice($tokenBundle, 1);
        }

        return array($variantTokenList, $paramTokenList);
    }

    /**
     * Accepts the bundled tokens for variant & parameter data and builds the Preference value object.
     *
     * @param array<string> $variantTokenList
     * @param array<array<string>> $paramBundleList
     * @param bool $serverField
     * @param string $fromField
     *
     * @return PreferenceInterface
     */
    private function createPreference(array $variantTokenList, array $paramBundleList, $serverField, $fromField)
    {
        $builder = $this->getBuilder($fromField)
            ->setFromField($fromField)
            ->setFromServer($serverField)
            ->setVariant(implode('', $variantTokenList));

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
     * Splits token list up into one bundle per variant for later processing.
     *
     * @param array<string> $tokenList
     * @param string $targetToken The token to split the list up by.
     *
     * @return array<array<string>> an array of arrays - the child array contains the tokens for a single variant.
     */
    private function bundleTokens(array $tokenList, $targetToken)
    {
        $bundleList = array();
        $bundle = array();

        foreach ($tokenList as $token) {
            // On token match add the bundle to list & re-initialize empty bundle
            if ($targetToken === $token) {
                $bundleList[] = $bundle;
                $bundle = array();

            // Otherwise collect tokens
            } else {
                $bundle[] = $token;
            }
        }

        // Handle trailing type
        $bundleList[] = $bundle;

        // Remove empty types
        $bundleList = array_filter($bundleList);

        return $bundleList;
    }

    /**
     * Checks to see if the bundle is valid for a mime type, if an anomaly is detected then an exception is thrown.
     *
     * @throws InvalidVariantException
     *
     * @param array<string> $bundle
     */
    private function validateBundleMimeVariant(array $bundle)
    {
        if (count($bundle) < 3                          // Too few items in bundle
            || Tokens::MIME_SEPARATOR !== $bundle[1]    // Invalid separator
            || Tokens::isSeparator($bundle[0], true)    // Invalid type
            || Tokens::isSeparator($bundle[2], true)    // Invalid subtype
        ) {
            throw new InvalidVariantException(
                '"' . implode('', $bundle) . '" is not a valid mime type'
            );
        }
    }

    /**
     * Checks to see if the parameters are correctly formed, if an anomaly is detected then an exception is thrown.
     *
     * @throws InvalidVariantException
     *
     * @param array<array<string>> $paramBundleList
     * @param bool $serverField     If true the field came from the server & we error on malformed data otherwise
     *                              we suppress errors for client preferences.
     */
    private function validateParamBundleList(array $paramBundleList, $serverField)
    {
        foreach ($paramBundleList as $paramBundle) {
            try {
                $this->validateParamBundle($paramBundle);
            } catch (InvalidVariantException $e) {
                // Rethrow exception only if the field was provided by the server
                if ($serverField) {
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
     * @throws InvalidVariantException
     *
     * @param string[] $paramBundle
     */
    private function validateParamBundle(array $paramBundle)
    {
        // Wrong number of components
        if (1 !== count($paramBundle) && 3 !== count($paramBundle)) {
            throw new InvalidVariantException(
                'Invalid count for parameters; expecting 1 or 3, got "' . count($paramBundle) . '"'
            );
        }
    }

    /**
     * Get a preference builder for the specified HTTP field.
     *
     * @param string $fromField
     *
     * @return PreferenceBuilderInterface
     */
    private function getBuilder($fromField)
    {
        if (PreferenceInterface::MIME === $fromField) {
            return $this->mimePrefBuilder;
        } else {
            return $this->prefBuilder;
        }
    }
}
