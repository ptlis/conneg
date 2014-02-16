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
     * Return a collection of charset types sorted by preference.
     *
     * @param TypeCollection $userTypeList
     * @param TypeCollection $appTypeList
     *
     * @return TypePairCollection
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
