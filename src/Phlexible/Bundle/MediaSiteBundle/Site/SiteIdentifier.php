<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Brainbits_Identifier as Identifier;

/**
 * Site identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class SiteIdentifier extends Identifier
{
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
    }
}
