<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Model;

use Phlexible\Component\Identifier\Identifier;

/**
 * Folder identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FolderIdentifier extends Identifier
{
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
    }
}
