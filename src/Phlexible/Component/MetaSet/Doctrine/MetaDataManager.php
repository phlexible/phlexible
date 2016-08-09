<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Psr\Log\LoggerInterface;

/**
 * Meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataManager implements MetaDataManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DataSourceManagerInterface
     */
    private $dataSourceManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param EntityManager              $entityManager
     * @param DataSourceManagerInterface $dataSourceManager
     * @param LoggerInterface            $logger
     * @param string                     $tableName
     */
    public function __construct(EntityManager $entityManager, DataSourceManagerInterface $dataSourceManager, LoggerInterface $logger, $tableName)
    {
        $this->entityManager = $entityManager;
        $this->dataSourceManager = $dataSourceManager;
        $this->logger = $logger;
        $this->tableName = $tableName;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSetAndIdentifiers(MetaSet $metaSet, array $identifiers)
    {
        $metaDatas = $this->doFindByMetaSetAndIdentifiers($metaSet, $identifiers);

        if (!count($metaDatas)) {
            return null;
        }

        return current($metaDatas);
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSet(MetaSet $metaSet)
    {
        return $this->doFindByMetaSetAndIdentifiers($metaSet);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->doFindByMetaSetAndIdentifiers();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        $metaData = new MetaData();
        $metaData->setMetaSet($metaSet);

        return $metaData;
    }

    /**
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData(MetaDataInterface $metaData)
    {
        $baseData = [
            'set_id' => $metaData->getMetaSet()->getId(),
        ];
        foreach ($metaData->getIdentifiers() as $field => $value) {
            $baseData[$field] = $value;
        }

        $connection = $this->getConnection();

        foreach ($metaData->getLanguages() as $language) {
            foreach ($metaData->getMetaSet()->getFields() as $field) {

                // TODO: löschen?
                if (!$metaData->get($field->getName(), $language)) {
                    continue;
                }

                $value = $metaData->get($field->getName(), $language);

                if ('suggest' === $field->getType()) {
                    $dataSourceId = $field->getOptions();
                    $dataSource = $this->dataSourceManager->find($dataSourceId);
                    foreach (explode(',', $value) as $singleValue) {
                        $dataSource->addValueForLanguage($language, $singleValue, true);
                    }
                    $this->dataSourceManager->updateDataSource($dataSource);
                }

                $insertData = $baseData;

                $insertData['id'] = Uuid::generate();
                $insertData['field_id'] = $field->getId();
                $insertData['value'] = $value;
                $insertData['language'] = $language;

                $connection->insert($this->getTableName(), $insertData);
            }
        }

        // TODO: job!
        //$this->_queueDataSourceCleanup();
    }

    /**
     * @param MetaSet $metaSet
     * @param array   $identifiers
     *
     * @return MetaData[]
     */
    private function doFindByMetaSetAndIdentifiers(MetaSet $metaSet = null, array $identifiers = [])
    {
        $connection = $this->getConnection();

        $qb = $connection->createQueryBuilder();
        $qb
            ->select('m.*')
            ->from($this->getTableName(), 'm');

        if ($metaSet) {
            $qb->where($qb->expr()->eq('m.set_id', $qb->expr()->literal($metaSet->getId())));
        }

        foreach ($identifiers as $field => $value) {
            $qb->andWhere($qb->expr()->eq("m.$field", $qb->expr()->literal($value)));
        }

        $rows = $connection->fetchAll($qb->getSQL());

        $metaDatas = [];

        foreach ($rows as $row) {
            $id = '';
            foreach ($identifiers as $value) {
                $id .= $value . '_';
            }
            $id .= $row['set_id'];

            if (!isset($metaDatas[$id])) {
                $metaData = new MetaData();
                $metaData
                    ->setIdentifiers($identifiers)
                    ->setMetaSet($metaSet);
                $metaDatas[$id] = $metaData;
            } else {
                $metaData = $metaDatas[$id];
            }

            $field = $metaSet->getFieldById($row['field_id']);

            if (!$field) {
                $this->logger->error(sprintf("MetaSet with id %s doesn't exists", $row['field_id']));
                $this->logger->info("MetaSet Identifieres", $identifiers);
                continue;
            }
            $metaData->set($field->getName(), $row['value'], $row['language']);
        }

        return $metaDatas;
    }

}
