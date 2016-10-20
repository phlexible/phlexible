<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
