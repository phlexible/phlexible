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
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

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
    }

    /**
     * @return CacheItemRepository
     */
    private function getCacheRepository()
    {
        if ($this->cacheRepository === null) {
            $this->cacheRepository = $this->entityManager->getRepository('PhlexibleMediaCacheBundle:CacheItem');
        }

        return $this->cacheRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getCacheRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCacheRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getCacheRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->getCacheRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFile($fileId, $fileVersion = null)
    {
        $criteria = ['fileId' => $fileId];
        if ($fileVersion) {
            $criteria['fileVersion'] = $fileVersion;
        }

        return $this->getCacheRepository()->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTemplateAndFile($templateKey, $fileId, $fileVersion)
    {
        return $this->cacheRepository->findOneBy(['templateKey' => $templateKey, 'fileId' => $fileId, 'fileVersion' => $fileVersion]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOutdatedTemplates(TemplateInterface $template)
    {
        $qb = $this->getCacheRepository()->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.templateKey', $qb->expr()->literal($template->getKey())))
            ->andWhere($qb->expr()->neq('c.templateRevision', $qb->expr()->literal($template->getRevision())));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function updateCacheItem(CacheItem $cacheItem)
    {
        $this->entityManager->persist($cacheItem);
        $this->entityManager->flush($cacheItem);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCacheItem(CacheItem $cacheItem)
    {
        // TODO: remove files
        $this->entityManager->remove($cacheItem);
        $this->entityManager->flush();
    }
}
