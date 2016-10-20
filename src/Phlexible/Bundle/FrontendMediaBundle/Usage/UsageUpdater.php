<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

    public function removeObsolete()
    {
        $this->fileUsageUpdater->removeObsolete();
        $this->folderUsageUpdater->removeObsolete();
    }

    /**
     * @param int $eid
     */
    public function removeUsage($eid)
    {
        $this->fileUsageUpdater->removeUsage($eid);
        $this->folderUsageUpdater->removeUsage($eid);
    }

    /**
     * @param Element $element
     */
    public function updateUsage(Element $element)
    {
        $this->fileUsageUpdater->updateUsage($element);
        $this->folderUsageUpdater->updateUsage($element);
    }
}
