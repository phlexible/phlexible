<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Identifier;

use Phlexible\Component\Identifier\Exception\InvalidArgumentException;

/**
 * Identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Identifier implements IdentifierInterface
{
    /**
     * @var array
     */
    private $delimiter = '__';

    /**
     * @var array
     */
    private $args = array();

    /**
     * Create a new identifier based on the given parameters
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $args = func_get_args();

        if (!count($args) || !implode('', $args)) {
            throw new InvalidArgumentException('No identifiers received');
        }

        array_unshift($args, str_replace('\\', '-', get_class($this)));

        $this->args = $args;
    }

    /**
     * Return dtring representation of this identifier
     *
     * @return string
     */
    public function __toString()
    {
        return str_replace('-', '_', implode($this->delimiter, $this->args));
    }
}
