<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\File;

use Brainbits_Identifier as Identifier;

/**
 * File identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FileIdentifier extends Identifier
{
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
    }
}
