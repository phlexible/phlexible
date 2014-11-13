<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Identifier;

/**
 * Identifiable interface
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
interface IdentifiableInterface
{
    /**
     * Return the identifier for this object
     *
     * @return IdentifierInterface
     */
    public function getIdentifier();
}
