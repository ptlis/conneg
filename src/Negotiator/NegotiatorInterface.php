<?php

/**
 * Interface class for negotiators.
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

use ptlis\ConNeg\Collection\CollectionInterface;
use ptlis\ConNeg\Collection\TypeCollection;
use ptlis\ConNeg\Type\TypeInterface;

/**
 * Interface class for negotiators.
 */
interface NegotiatorInterface
{
    /**
     * Return a collection of types sorted by preference.
     *
     * @param TypeCollection|TypeInterface[] $userTypeList
     * @param TypeCollection|TypeInterface[] $appTypeList
     *
     * @return CollectionInterface
     */
    public function negotiateAll(TypeCollection $userTypeList, TypeCollection $appTypeList);


    /**
     * Return the preferred type & product of application & user-agent quality factors.
     *
     * @param TypeCollection|TypeInterface[] $userTypeList
     * @param TypeCollection|TypeInterface[] $appTypeList
     *
     * @return TypeInterface
     */
    public function negotiateBest(TypeCollection $userTypeList, TypeCollection $appTypeList);
}
