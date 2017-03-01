<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Model;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;

/**
 * Siteroot manager interface.
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
interface SiterootManagerInterface
{
    /**
     * @param string $id
     *
     * @return null|Siteroot
     */
    public function find($id);

    /**
     * @return Siteroot[]
     */
    public function findAll();

    /**
     * @param Siteroot $siteroot
     */
    public function updateSiteroot(Siteroot $siteroot);

    /**
     * @param Siteroot $siteroot
     */
    public function deleteSiteroot(Siteroot $siteroot);
}
