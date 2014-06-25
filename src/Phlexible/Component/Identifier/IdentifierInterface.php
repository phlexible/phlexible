<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Identifier;

/**
 * Identifier interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface IdentifierInterface
{
    /**
     * @return string
     */
    public function __toString();
}
