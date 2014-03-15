<?php

/**
 * Class for negotiating on language types.
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

namespace ptlis\ConNeg\Type\Language;

use ptlis\ConNeg\Collection\SharedTypePairCollection;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Type\Shared\SharedNegotiator;
use ptlis\ConNeg\Type\Shared\Interfaces\NegotiatorInterface;
use ptlis\ConNeg\TypePair\TypePairInterface;

/**
 * Class for negotiating on language types.
 */
class LanguageNegotiator implements NegotiatorInterface
{
    /**
     * @var SharedNegotiator
     */
    private $sharedNegotiator;


    /**
     * Constructor
     *
     * @param SharedNegotiator $sharedNegotiator
     */
    public function __construct(SharedNegotiator $sharedNegotiator)
    {
        $this->sharedNegotiator = $sharedNegotiator;
    }


    /**
     * Return a collection of language types sorted by preference.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return SharedTypePairCollection
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList)
    {
        return $this->sharedNegotiator->negotiateAll($userTypeList, $appTypeList);
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
        return $this->sharedNegotiator->negotiateBest($userTypeList, $appTypeList);
    }
}
