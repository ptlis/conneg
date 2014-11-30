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

namespace ptlis\ConNeg\Type\Extens;

/**
 * Implementation of a fragment from the accept-extens parameters
 */
class AcceptExtens implements AcceptExtensInterface
{
    /**
     * The value from the accept-extens fragment.
     *
     * @var string
     */
    private $value;

    /**
     * The (optional) key from the accept-extens fragment.
     *
     * @var string
     */
    private $key;

    /**
     * Constructor.
     *
     * @param string $value
     * @param string $key
     */
    public function __construct($value, $key = '')
    {
        $this->value = $value;
        $this->key = $key;
    }

    /**
     * Returns true if type has a key & value, false if only a value.
     *
     * @return bool
     */
    public function isCompound()
    {
        return strlen($this->key) > 0;
    }

    /**
     * Returns the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns a string representation of the accept-extens fragment.
     *
     * @return string
     */
    public function __toString()
    {
        $string = '';
        if ($this->isCompound()) {
            $string = $this->key . '=';
        }
        $string .= $this->value;

        return $string;
    }
}
