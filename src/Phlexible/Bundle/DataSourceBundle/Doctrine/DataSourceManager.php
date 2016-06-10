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
    public function updateDataSource(DataSource $dataSource, $flush = true)
    {
        $this->entityManager->persist($dataSource);
        foreach ($dataSource->getValueBags() as $value) {
            $this->entityManager->persist($value);
        }
        if ($flush) {
            $this->entityManager->flush($dataSource);
        }
        foreach ($dataSource->getValueBags() as $value) {
            $this->entityManager->flush($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDataSource(DataSource $dataSource)
    {
        $this->entityManager->remove($dataSource);
        $this->entityManager->flush($dataSource);
    }
}
