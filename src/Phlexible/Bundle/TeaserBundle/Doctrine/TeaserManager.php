<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Event\PublishTeaserEvent;
use Phlexible\Bundle\TeaserBundle\Event\SetTeaserOfflineEvent;
use Phlexible\Bundle\TeaserBundle\Event\TeaserEvent;
use Phlexible\Bundle\TeaserBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Teaser manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserManager implements TeaserManagerInterface
{
    const TYPE_TEASER = 'teaser';
    const TYPE_CATCH = 'catch';
    const TYPE_INHERITED = 'inherited';
    const TYPE_STOP = 'stop';
    const TYPE_HIDE = 'hide';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $elementHistoryManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $teaserRepository;

    /**
     * @param EntityManager                  $entityManager
     * @param StateManagerInterface          $stateManager
     * @param ElementHistoryManagerInterface $elementHistoryManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        StateManagerInterface $stateManager,
        ElementHistoryManagerInterface $elementHistoryManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->stateManager = $stateManager;
        $this->elementHistoryManager = $elementHistoryManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getTeaserRepository()
    {
        if (null === $this->teaserRepository) {
            $this->teaserRepository = $this->entityManager->getRepository('PhlexibleTeaserBundle:Teaser');
        }

        return $this->teaserRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getTeaserRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getTeaserRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->getTeaserRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findForLayoutAreaAndTreeNodePath($layoutarea, array $treeNodePath, $includeLocalHidden = true)
    {
        /* @var $teasers Teaser[] */
        $teasers = [];
        $forTreeId = end($treeNodePath)->getId();

        foreach ($treeNodePath as $treeNode) {
            /* @var $treeNode TreeNodeInterface */
            foreach ($this->findForLayoutAreaAndTreeNode($layoutarea, $treeNode) as $teaser) {
                if ($treeNode->getId() !== $forTreeId && $teaser->hasStopId($treeNode->getId())) {
                    continue;
                }
                if ($teaser->hasStopId($forTreeId)) {
                    $teaser->setStopped();
                }
                if ($teaser->hasHideId($forTreeId)) {
                    $teaser->setHidden();
                }

                $teasers[$teaser->getId()] = $teaser;
            }

            if ($treeNode->getId() !== $forTreeId) {
                foreach ($teasers as $index => $teaser) {
                    if ($teaser->hasStopId($treeNode->getId())) {
                        unset($teasers[$index]);
                    }
                }
            } elseif (!$includeLocalHidden) {
                foreach ($teasers as $index => $teaser) {
                    if ($teaser->isHidden()) {
                        unset($teasers[$index]);
                    }
                }
            }
        }

        return $teasers;
    }

    /**
     * {@inheritdoc}
     */
    public function findForLayoutAreaAndTreeNode($layoutarea, TreeNodeInterface $treeNode)
    {
        $teasers = $this->getTeaserRepository()->findBy(
            [
                'layoutareaId' => $layoutarea->getId(),
                'treeId'       => $treeNode->getId()
            ],
            array('sort' => 'ASC')
        );

        return $teasers;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(Teaser $teaser)
    {
        $qb = $this->getTeaserRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->eq('t.type', $qb->expr()->literal($teaser->getType())))
            ->andWhere($qb->expr()->eq('t.typeId', $teaser->getTypeId()));

        return $qb->getQuery()->getSingleScalarResult() > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(Teaser $teaser)
    {
        return $teaser->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(Teaser $teaser)
    {
        return $this->getTeaserRepository()->findBy(
            [
                'type'   => $teaser->getType(),
                'typeId' => $teaser->getTypeId(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(Teaser $teaser, $language)
    {
        return $this->stateManager->isPublished($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(Teaser $teaser)
    {
        return $this->stateManager->getPublishedLanguages($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(Teaser $teaser, $language)
    {
        return $this->stateManager->getPublishedVersion($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(Teaser $teaser)
    {
        return $this->stateManager->getPublishedVersions($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        return $this->stateManager->isAsync($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTeaser(Teaser $teaser)
    {
        return $this->stateManager->findByTeaser($teaser);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTeaserAndLanguage(Teaser $teaser, $language)
    {
        return $this->stateManager->findOneByTeaserAndLanguage($teaser, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function createTeaser(
        $treeId,
        $eid,
        $layoutareaId,
        $type,
        $typeId,
        $prevId = 0,
        array $stopIds = null,
        array $hideIds = null,
        $masterLanguage = 'en',
        $userId
    )
    {
        $teaser = new Teaser();
        $teaser
            ->setTreeId($treeId)
            ->setEid($eid)
            ->setLayoutareaId($layoutareaId)
            ->setType($type)
            ->setTypeId($typeId)
            ->setSort(0)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        if ($stopIds) {
            $teaser->setStopIds($stopIds);
        }

        if ($hideIds) {
            $teaser->setHideIds($hideIds);
        }

        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_TEASER, $event)->isPropagationStopped()) {
            return null;
        }

        $this->entityManager->persist($teaser);
        $this->entityManager->flush($teaser);

        // @TODO: sort
        /*
            $sort = 0;
            if ($prevId) {
                $select = $db->select()
                    ->from($db->prefix . 'element_tree_teasers', 'sort + 1')
                    ->where('id = ?', $prevId);

                $sort = $db->fetchOne($select);
            }

            $db->update(
                $db->prefix . 'element_tree_teasers',
                array('sort' => ('sort + 1')),
                array('tree_id = ?' => $treeId, 'sort >= ?' => $sort)
            );
        */

        if ($type === 'element') {
            $this->elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_TEASER,
                $teaser->getTypeId(),
                $userId,
                null,
                $teaser->getId()
            );
        }

        $event = new TeaserEvent($teaser);
        $this->dispatcher->dispatch(TeaserEvents::CREATE_TEASER, $event);

        return $teaser;
    }

    /**
     * {@inheritdoc}
     */
    public function createTeaserInstance(TreeNodeInterface $treeNode, Teaser $teaser, $layoutAreaId, $userId)
    {
        $teaser = clone $teaser;
        $teaser
            ->setId(null)
            ->setTreeId($treeNode->getId())
            ->setLayoutareaId($layoutAreaId)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_TEASER_INSTANCE, $event)->isPropagationStopped()) {
            return null;
        }

        $this->entityManager->persist($teaser);
        $this->entityManager->flush($teaser);

        if ($teaser->getType() === 'element') {
            $this->elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_TEASER_INSTANCE,
                $teaser->getTypeId(),
                $userId,
                null,
                $teaser->getId()
            );
        }

        $event = new TeaserEvent($teaser);
        $this->dispatcher->dispatch(TeaserEvents::CREATE_TEASER_INSTANCE, $event);

        return $teaser;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTeaser(Teaser $teaser, $flush = true)
    {
        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_UPDATE_TEASER, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->persist($teaser);
        if ($flush) {
            $this->entityManager->flush($teaser);
        }

        $event = new TeaserEvent($teaser);
        $this->dispatcher->dispatch(TeaserEvents::UPDATE_TEASER, $event);

    }

    /**
     * {@inheritdoc}
     */
    public function updateTeasers(array $teasers, $flush = true)
    {
        foreach ($teasers as $teaser) {
            $this->updateTeaser($teaser, false);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTeaser(Teaser $teaser, $userId)
    {
        $event = new TeaserEvent($teaser);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_DELETE_TEASER, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($teaser);
        $this->entityManager->flush();

        if ($teaser->getType() === 'element') {
            $this->elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_DELETE_TEASER,
                $teaser->getTypeId(),
                $userId,
                null,
                $teaser->getId()
            );
        }

        $event = new TeaserEvent($teaser);
        $this-> dispatcher->dispatch(TeaserEvents::DELETE_TEASER, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function publishTeaser(Teaser $teaser, $version, $language, $userId, $comment = null)
    {
        $event = new PublishTeaserEvent($teaser, $language, $version);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_PUBLISH_TEASER, $event)->isPropagationStopped()) {
            return null;
        }

        $teaserOnline = $this->stateManager->publish($teaser, $version, $language, $userId);

        $this->elementHistoryManager->insert(
            ElementHistoryManagerInterface::ACTION_PUBLISH_TEASER,
            $teaser->getTypeId(),
            $userId,
            null,
            $teaser->getId(),
            $version,
            $language,
            $comment
        );

        $event = new PublishTeaserEvent($teaser, $language, $version);
        $this->dispatcher->dispatch(TeaserEvents::PUBLISH_TEASER, $event);

        return $teaserOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setTeaserOffline(Teaser $teaser, $language, $userId, $comment = null)
    {
        $event = new SetTeaserOfflineEvent($teaser, $language);
        if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_SET_TEASER_OFFLINE, $event)->isPropagationStopped()) {
            return null;
        }

        $this->stateManager->setOffline($teaser, $language);

        $this->elementHistoryManager->insert(
            ElementHistoryManagerInterface::ACTION_PUBLISH_TEASER,
            $teaser->getTypeId(),
            $userId,
            null,
            $teaser->getId(),
            null,
            $language,
            $comment
        );

        $event = new SetTeaserOfflineEvent($teaser, $language);
        $this->dispatcher->dispatch(TeaserEvents::SET_TEASER_OFFLINE, $event);
    }
}
