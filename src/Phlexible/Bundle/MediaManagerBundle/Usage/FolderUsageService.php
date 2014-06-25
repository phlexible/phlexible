<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Folder usage service
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FolderUsageService
{
    /**
     * @var FolderUsageRepository
     */
    private $folderUsageRepository;

    /**
     * @param FolderUsageRepository $folderUsageRepository
     */
    public function __construct(FolderUsageRepository $folderUsageRepository)
    {
        $this->folderUsageRepository = $folderUsageRepository;
    }

    /**
     * Return aggregated status
     *
     * @param FolderInterface $folder
     *
     * @return int
     */
    public function getStatus(FolderInterface $folder)
    {
        $status = 0;
        foreach ($this->folderUsageRepository->findByFolder($folder) as $folderUsage) {
            $status &= $folderUsage->getStatus();
        }

        return $status;
    }
}