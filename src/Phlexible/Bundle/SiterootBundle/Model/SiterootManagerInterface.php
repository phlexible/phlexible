<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Model;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;

/**
 * Siteroot manager interface
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
