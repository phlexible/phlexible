<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Entity\Repository\CacheItemRepository;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;

/**
 * Doctrine cache manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheManager implements CacheManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CacheItemRepository
     */
    private $cacheRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->cacheRepository = $entityManager->getRepository('PhlexibleMediaCacheBundle:CacheItem');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->cacheRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFile($fileId, $fileVersion = null)
    {
        $criteria = array('fileId' => $fileId);
        if ($fileVersion) {
            $criteria['fileVersion'] = $fileVersion;
        }

        return $this->cacheRepository->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTemplateAndFile($templateKey, $fileId, $fileVersion)
    {
        return $this->cacheRepository->findOneBy(array('templateKey' => $templateKey, 'fileId' => $fileId, 'fileVersion' => $fileVersion));
    }

    /**
     * {@inheritdoc}
     */
    public function updateCacheItem(CacheItem $cacheItem)
    {
        $this->entityManager->persist($cacheItem);
        $this->entityManager->flush($cacheItem);
    }
}
