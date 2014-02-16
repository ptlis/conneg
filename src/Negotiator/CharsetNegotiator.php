<?php

/**
 * Class for negotiating on charset types.
 *
 * PHP Version 5.3
 *
 * @copyright   (c) 2006-2014 brian ridley
 * @author      brian ridley <ptlis@ptlis.net>
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ptlis\ConNeg\Negotiator;

use ptlis\ConNeg\Collection\TypePairCollection;
use ptlis\ConNeg\Type\AbsentType;
use ptlis\ConNeg\Type\Charset\CharsetTypeFactory;
use ptlis\ConNeg\Type\TypeInterface;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Type\WildcardType;
use ptlis\ConNeg\TypePair\TypePair;
use ptlis\ConNeg\TypePair\TypePairInterface;

/**
 * Class for negotiating on charset types.
 */
class CharsetNegotiator implements NegotiatorInterface
{
    /**
     * @var CharsetTypeFactory
     */
    private $charsetFactory;


    /**
     * Constructor
     *
     * @param CharsetTypeFactory $charsetFactory
     */
    public function __construct(CharsetTypeFactory $charsetFactory)
    {
        $this->charsetFactory = $charsetFactory;
    }


    /**
     * Return a collection of charset types sorted by preference.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return TypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $matchingList = array();
        foreach ($appTypeList as $appType) {
            $matchingList[$appType->getType()] = array(
                'appType' => $appType,
                'userType' => new AbsentType()
            );
        }

        foreach ($userTypeList as $userType) {

            // Type match
            if (array_key_exists($userType->getType(), $matchingList)) {
                $betterUserType = $this->preferredUserMatch(
                    $matchingList[$userType->getType()]['appType'],
                    $matchingList[$userType->getType()]['userType'],
                    $userType
                );

                if ($betterUserType) {
                    $matchingList[$userType->getType()]['userType'] = $userType;
                }

            // Wildcard Match
            } elseif ($userType instanceof WildcardType) {

                foreach ($matchingList as $key => $matching) {
                    if ($userType->getPrecedence() > $matching['userType']->getPrecedence()) {
                        $matchingList[$key]['userType'] = $userType;
                    }
                }

            // No match
            } else {
                $matchingList[$userType->getType()] = array(
                    'appType' => new AbsentType(),
                    'userType' => $userType
                );
            }
        }

        $pairCollection = new TypePairCollection();

        foreach ($matchingList as $matching) {
            $pairCollection->addPair(
                new TypePair($matching['appType'], $matching['userType'])
            );
        }

        return $pairCollection->getDescending();
    }


    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return TypePairInterface
     */
    public function negotiateBest(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        $pairCollection = $this->negotiateAll($userTypeList, $appTypeList);

        return $pairCollection->getBest();
    }


    /**
     * Returns true if the provided user-agent type matches the stored application type & supersedes the precedence of
     * the currently stored user-agent type or has a higher quality factor.
     *
     * @param TypeInterface $appType
     * @param TypeInterface $oldUserType
     * @param TypeInterface $newUserType
     *
     * @return bool
     */
    public function preferredUserMatch(
        TypeInterface $appType,
        TypeInterface $oldUserType,
        TypeInterface $newUserType
    ) {
        $preferred = false;

        if ($this->absentUserType($appType, $oldUserType, $newUserType)
                || $this->higherUserPrecedence($appType, $oldUserType, $newUserType)
                || $this->higherUserQualityFactor($appType, $oldUserType, $newUserType)) {
            $preferred = true;
        }

        return $preferred;
    }


    /**
     * Returns true if the stored user type is absent and the provided user type matches the stored application type.
     *
     * @param TypeInterface $appType
     * @param TypeInterface $oldUserType
     * @param TypeInterface $newUserType
     *
     * @return bool
     */
    private function absentUserType(
        TypeInterface $appType,
        TypeInterface $oldUserType,
        TypeInterface $newUserType
    ) {
        $absent = false;

        if ($newUserType->getType() == $appType->getType() && $oldUserType->getType() == '') {
            $absent = true;
        }

        return $absent;
    }


    /**
     * Returns true if the user and stored application types match and the provided type has a higher precedence.
     *
     * @param TypeInterface $appType
     * @param TypeInterface $oldUserType
     * @param TypeInterface $newUserType
     *
     * @return bool
     */
    private function higherUserPrecedence(
        TypeInterface $appType,
        TypeInterface $oldUserType,
        TypeInterface $newUserType
    ) {
        $higher = false;

        if ($newUserType->getType() === $appType->getType()
                && $newUserType->getPrecedence() > $oldUserType->getPrecedence()) {
            $higher = true;
        }

        return $higher;
    }


    /**
     * Returns true if the user and stored application types match, the user precedence values match, but the
     * provided quality factor is higher.
     *
     * @param TypeInterface $appType
     * @param TypeInterface $oldUserType
     * @param TypeInterface $newUserType
     *
     * @return bool
     */
    private function higherUserQualityFactor(
        TypeInterface $appType,
        TypeInterface $oldUserType,
        TypeInterface $newUserType
    ) {
        $higher = false;

        if ($newUserType->getType() === $appType->getType()
                && $newUserType->getPrecedence() === $oldUserType->getPrecedence()
                && $newUserType->getQualityFactor() > $oldUserType->getQualityFactor()) {
            $higher = true;
        }

        return $higher;
    }
}
