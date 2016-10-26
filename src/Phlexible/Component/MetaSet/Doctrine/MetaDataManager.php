<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;
use Phlexible\Component\MetaSet\Event\MetaDataValueEvent;
use Phlexible\Component\MetaSet\MetaSetEvents;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetFieldInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class MetaDataManager implements MetaDataManagerInterface
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManager            $entityManager
     * @param MetaSetManagerInterface  $metaSetManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->metaSetManager = $metaSetManager;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @return EntityRepository
     */
    protected function getDataRepository()
    {
        return $this->entityManager->getRepository($this->getDataClass());
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSet(MetaSetInterface $metaSet)
    {
        return $this->findByMetaSetAndTarget($metaSet, null);
    }

    /**
     * {@inheritdoc}
     */
    public function findRawByValue($value)
    {
        $dataRepository = $this->getDataRepository();
        $qb = $dataRepository->createQueryBuilder('d');
        $qb
            ->where($qb->expr()->like('d.value', $qb->expr()->literal("%$value%")));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findRawByField(MetaSetFieldInterface $field)
    {
        return $this->findMetaDataValues($field->getMetaSet(), null, $field->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetaData($target, MetaDataInterface $metaData)
    {
        foreach ($metaData->getLanguages() as $language) {
            foreach ($metaData->getMetaSet()->getFields() as $field) {

                // TODO: löschen?
                if (!$metaData->get($field->getName(), $language)) {
                    continue;
                }

                $value = $metaData->get($field->getName(), $language);

                $data = $this->getOrCreateMetaDataValue(
                    $metaData->getMetaSet()->getId(),
                    $language,
                    $field->getId(),
                    $target
                );
                $data->setValue($value);

                $event = new MetaDataValueEvent($data, $metaData->getMetaSet(), $field);
                $this->eventDispatcher->dispatch(MetaSetEvents::BEFORE_UPDATE_META_DATA_VALUE, $event);

                $this->entityManager->persist($data);
                $this->entityManager->flush($data);

                $event = new MetaDataValueEvent($data, $metaData->getMetaSet(), $field);
                $this->eventDispatcher->dispatch(MetaSetEvents::UPDATE_META_DATA_VALUE, $event);
            }
        }

        // TODO: job!
        //$this->_queueDataSourceCleanup();
    }

    /**
     * @return string
     */
    abstract protected function getDataClass();

    /**
     * @param QueryBuilder $qb
     * @param mixed        $target
     */
    abstract protected function joinTarget(QueryBuilder $qb, $target);

    /**
     * @param string $setId
     * @param string $language
     * @param string $fieldId
     * @param mixed  $target
     *
     * @return MetaDataValue
     */
    abstract protected function getOrCreateMetaDataValue($setId, $language, $fieldId, $target);

    /**
     * @param MetaSetInterface $metaSet
     * @param mixed            $target
     *
     * @return MetaDataInterface|null
     */
    protected function findOneByMetaSetAndTarget(MetaSetInterface $metaSet, $target)
    {
        $metaDatas = $this->findByMetaSetAndTarget($metaSet, $target);

        if (!count($metaDatas)) {
            return null;
        }

        return current($metaDatas);
    }

    /**
     * @param MetaSetInterface $metaSet
     * @param mixed            $target
     *
     * @return MetaDataInterface[]
     */
    protected function findByMetaSetAndTarget(MetaSetInterface $metaSet, $target = null)
    {
        $metaDatas = array();

        foreach ($this->findMetaDataValues($metaSet, $target) as $metaDataValue) {
            $identifier = $metaDataValue->getSetId();

            if (!isset($metaDatas[$identifier])) {
                $metaData = new MetaData($metaSet);
                $metaDatas[$identifier] = $metaData;
            } else {
                $metaData = $metaDatas[$identifier];
            }

            $field = $metaSet->getFieldById($metaDataValue->getFieldId());

            if (!$field) {
                $this->logger->error(sprintf("MetaSet with id %s doesn't exist", $metaDataValue->getFieldId()));
                continue;
            }

            $metaData->set($field->getName(), $metaDataValue->getValue(), $metaDataValue->getLanguage());
        }

        return $metaDatas;
    }

    /**
     * {@inheritdoc}
     */
    protected function findMetaDataValues(MetaSetInterface $metaSet, $target = null, $fieldId = null)
    {
        $dataRepository = $this->getDataRepository();
        $qb = $dataRepository->createQueryBuilder('d');
        $qb->where($qb->expr()->eq('d.setId', $qb->expr()->literal($metaSet->getId())));

        if ($target) {
            $this->joinTarget($qb, $target);
        }

        if ($fieldId) {
            $qb->andWhere($qb->expr()->eq('d.fieldId', $qb->expr()->literal($fieldId)));
        }

        return $qb->getQuery()->getResult();
    }
}
