<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Model;

/**
 * Domain object interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DomainObjectInterface
{
    /**
     * Return domain object identifier
     *
     * @return array
     */
    public function getObjectIdentifier();

    /**
     * Return domain object type
     *
     * @return array
     */
    public function getObjectType();
}
