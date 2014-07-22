<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\Exception;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Abstract driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
Abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder, $userId)
    {
        $this->deletePhysicalFolder($folder, $userId);

        try {
            $this->db->delete(
                $this->folderTable,
                array(
                    'site_id = ?' => $this->site->getId(),
                    'path LIKE ?' => $folder->getPath() . '%'
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Delete folder failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $folder = $this->findFolderByFileId($file->getId());
        $physicalPath = $this->site->getRootDir() . $folder->getPath() . '/' . $file->getName();

        if ($filesystem->exists($physicalPath)) {
            if (!is_file($physicalPath)) {
                throw new IOException('Delete file failed, not a file.');
            }

            if (!is_writable(dirname($physicalPath))) {
                throw new NotWritableException("Delete file failed.");
            }
        }

        $this->db->delete(
            $this->fileTable,
            array(
                'id = ?' => $file->getId()
            )
        );

        if ($filesystem->exists($physicalPath)) {
            $filesystem->remove($physicalPath);
        }
    }
}
