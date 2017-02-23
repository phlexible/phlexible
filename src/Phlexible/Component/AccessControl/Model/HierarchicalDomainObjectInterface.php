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
 * Hierarchical domain object interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HierarchicalDomainObjectInterface extends DomainObjectInterface
{
    /**
     * Return hierarchical domain identifier path.
     *
     * @return array
     */
    public function getHierarchicalObjectIdentifiers();
}
