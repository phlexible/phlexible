<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Usage;

use Phlexible\Bundle\ElementBundle\Entity\Element;

/**
 * Usage updater
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UsageUpdater
{
    /**
     * @var FileUsageUpdater
     */
    private $fileUsageUpdater;

    /**
     * @var FolderUsageUpdater
     */
    private $folderUsageUpdater;

    /**
     * @param FileUsageUpdater   $fileUsageUpdater
     * @param FolderUsageUpdater $folderUsageUpdater
     */
    public function __construct(FileUsageUpdater $fileUsageUpdater, FolderUsageUpdater $folderUsageUpdater)
    {
        $this->fileUsageUpdater = $fileUsageUpdater;
        $this->folderUsageUpdater = $folderUsageUpdater;
    }

    /**
     * @param Element $element
     *
     * @return array
     */
    public function updateUsage(Element $element)
    {
        $this->fileUsageUpdater->updateUsage($element);
        $this->folderUsageUpdater->updateUsage($element);
    }
}
