<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Meta;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Psr\Log\LoggerInterface;

/**
 * Element meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMetaDataManager extends MetaDataManager
{
//    /**
//     * ElementMetaDataManager constructor.
//     * @param EntityManager $entityManager
//     * @param DataSourceManagerInterface $dataSourceManager
//     * @param LoggerInterface $logger
//     * @param string $tableName
//     */
//    public function __construct(EntityManager $entityManager, DataSourceManagerInterface $dataSourceManager, LoggerInterface $logger, $tableName)
//    {
//        parent::__construct($entityManager, $dataSourceManager, $logger, $tableName);
//    }


    /**
     * @param MetaSet        $metaSet
     * @param ElementVersion $elementVersion
     *
     * @return MetaData|MetaDataInterface
     */
    public function createElementMetaData(MetaSet $metaSet, ElementVersion $elementVersion)
    {
        $metaData = $this->createMetaData($metaSet);
        $metaData
            ->setIdentifiers($this->getIdentifiersFromElementVersion($elementVersion));

        return $metaData;
    }

    /**
     * @param MetaSet        $metaSet
     * @param ElementVersion $elementVersion
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndElementVersion(MetaSet $metaSet, ElementVersion $elementVersion)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromElementVersion($elementVersion));
    }

    /**
     * @param string $value
     *
     * @return MetaDataInterface[]
     */
    public function findByValue($value)
    {
        $connection = $this->getConnection();

        $qb = $connection->createQueryBuilder();
        $qb
            ->select('m.*')
            ->from($this->getTableName(), 'm')
            ->where($qb->expr()->like('m.value', $qb->expr()->literal("%$value%")));

        $rows = $connection->fetchAll($qb->getSQL());

        $metaDatas = [];

        foreach ($rows as $row) {
            $identifiers = [
                'eid'     => $row['eid'],
                'version' => $row['version'],
            ];

            $id = '';
            foreach ($identifiers as $value) {
                $id .= $value . '_';
            }
            $id .= $row['set_id'];

            if (!isset($metaDatas[$id])) {
                $metaData = new MetaData();
                $metaData
                    ->setIdentifiers($identifiers)
                    ->setMetaSet(null);
                $metaDatas[$id] = $metaData;
            } else {
                $metaData = $metaDatas[$id];
            }

            $metaData->set($row['field_id'], $row['value'], $row['language']);
        }

        return $metaDatas;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return array
     */
    private function getIdentifiersFromElementVersion(ElementVersion $elementVersion)
    {
        return [
            'eid'     => $elementVersion->getElement()->getEid(),
            'version' => $elementVersion->getVersion(),
        ];
    }
}
