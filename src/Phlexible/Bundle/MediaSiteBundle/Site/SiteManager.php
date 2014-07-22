<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Phlexible\Bundle\MediaSiteBundle\Exception\NotFoundException;

/**
 * Site manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteManager
{
    /**
     * @var Site[]
     */
    private $sites = array();

    /**
     * @var array
     */
    private $idToKey = array();

    /**
     * @param array $sites
     */
    public function __construct(array $sites)
    {
        $this->sites = $sites;

        foreach ($sites as $key => $site) {
            $this->idToKey[$site->getId()] = $key;
        }
    }

    /**
     * @param string $key
     *
     * @return Site
     */
    public function get($key)
    {
        return $this->sites[$key];
    }

    /**
     * @param string $id
     *
     * @return Site
     */
    public function getSiteById($id)
    {
        return $this->get($this->idToKey[$id]);
    }

    /**
     * Return site by file ID
     *
     * @param string $fileId
     *
     * @return Site
     * @throws NotFoundException
     */
    public function getByFileId($fileId)
    {
        foreach ($this->sites as $site) {
            try {
                $file = $site->findFile($fileId);
                if ($file) {
                    return $site;
                }
            } catch (\Exception $e) {
            }
        }

        throw new NotFoundException("Site for file $fileId not found.");
    }

    /**
     * Return site by folder ID
     *
     * @param string $folderId
     *
     * @return Site
     * @throws NotFoundException
     */
    public function getByFolderId($folderId)
    {
        foreach ($this->sites as $site) {
            try {
                $site->findFolder($folderId);

                return $site;
            } catch (\Exception $e) {
            }
        }

        throw new NotFoundException("Site for folder $folderId not found.");
    }

    /**
     * Return all sites
     *
     * @return Site[]
     */
    public function getAll()
    {
        return $this->sites;
    }
}
