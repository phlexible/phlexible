<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle;

use Phlexible\Bundle\MediaSiteBundle\File;
use Phlexible\Bundle\MediaSiteBundle\Folder;
use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Media manager message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array('component' => 'mediamanager');
    }
}