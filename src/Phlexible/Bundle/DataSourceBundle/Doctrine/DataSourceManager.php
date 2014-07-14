<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DataSourceBundle\DataSource\DataSourceRepository;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;

/**
 * Doctrine data source manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSourceManager implements DataSourceManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DataSourceRepository
     */
    private $dataSourceRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->dataSourceRepository = $entityManager->getRepository('PhlexibleDataSourceBundle:DataSource');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->dataSourceRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->dataSourceRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDataSourceTitles($sorted = false)
    {
        return $this->dataSourceRepository->getAllDataSourceTitles($sorted);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDataSourceLanguages($dataSourceId)
    {
        return $this->dataSourceRepository->getAllDataSourceLanguages($dataSourceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDataSourceIds()
    {
        return $this->dataSourceRepository->getAllDataSourceIds();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllValuesByDataSourceId($sourceId, $language, $isActive = null)
    {
        return $this->dataSourceRepository->getAllValuesByDataSourceId($sourceId, $language, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function updateDataSource(DataSource $dataSource)
    {
        $this->entityManager->persist($dataSource);
        foreach ($dataSource->getValueBags() as $value) {
            $this->entityManager->persist($value);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDataSource(DataSource $dataSource)
    {
        $this->entityManager->remove($dataSource);
        $this->entityManager->flush();
    }
}