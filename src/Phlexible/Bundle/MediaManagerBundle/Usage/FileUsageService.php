<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * File usage service
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FileUsageService
{
    /**
     * @var FileUsageRepository
     */
    private $fileUsageRepository;

    /**
     * @param FileUsageRepository $fileUsageRepository
     */
    public function __construct(FileUsageRepository $fileUsageRepository)
    {
        $this->fileUsageRepository = $fileUsageRepository;
    }

    /**
     * Return aggregated status
     *
     * @param FileInterface $file
     *
     * @return int
     */
    public function getStatus(FileInterface $file)
    {
        $status = 0;
        foreach ($this->fileUsageRepository->findByFile($file) as $fileUsage) {
            $status &= $fileUsage->getStatus();
        }

        return $status;
    }

    /**
     * Return highest aggregated status
     *
     * @param FileInterface $file
     *
     * @return int
     */
    public function getHighestStatus(FileInterface $file)
    {
        $status = $this->getStatus($file);

        if ($status & 8) {
            return 8;
        } elseif ($status & 4) {
            return 4;
        } elseif ($status & 2) {
            return 2;
        } elseif ($status & 1) {
            return 1;
        }

        return 0;
    }

    /**
     * Return aggregated status
     *
     * @param FileInterface $file
     *
     * @return array
     */
    public function getUsedIn(FileInterface $file)
    {
        $usedIn = array();
        foreach ($this->fileUsageRepository->findByFile($file) as $fileUsage) {
            $usedIn[] = array(
                'usage_type' => $fileUsage->getUsageType(),
                'usage_id'   => $fileUsage->getUsageId(),
                'status'     => $fileUsage->getStatus(),
            );
        }

        return $usedIn;
    }
}