<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;
use Phlexible\Bundle\TreeBundle\Mediator\Mediator;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\NodeHasher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * State manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StateManager implements StateManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @var Mediator
     */
    private $mediator;

    /**
     * @var NodeHasher
     */
    private $nodeHasher;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $treeNodeOnlineRepository;

    private $cache = array();

    /**
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param Mediator                       $mediator
     * @param NodeHasher                     $nodeHasher
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        Mediator $mediator,
        NodeHasher $nodeHasher,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->historyManager = $historyManager;
        $this->mediator = $mediator;
        $this->nodeHasher = $nodeHasher;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getTreeNodeOnlineRepository()
    {
        if (null === $this->treeNodeOnlineRepository) {
            $this->treeNodeOnlineRepository = $this->entityManager->getRepository('PhlexibleTreeBundle:TreeNodeOnline');
        }

        return $this->treeNodeOnlineRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findByTreeNode(TreeNodeInterface $treeNode)
    {
        $id = $treeNode->getId();

        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getTreeNodeOnlineRepository()->findBy(['treeNode' => $treeNode->getId()]);
        }

        return $this->cache[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTreeNodeAndLanguage(TreeNodeInterface $treeNode, $language)
    {
        $id = $treeNode->getId().'_'.$language;

        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getTreeNodeOnlineRepository()->findOneBy(['treeNode' => $treeNode->getId(), 'language' => $language]);
        }

        return $this->cache[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(TreeNodeInterface $treeNode, $language)
    {
        return $this->findOneByTreeNodeAndLanguage($treeNode, $language) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(TreeNodeInterface $treeNode)
    {
        $language = [];
        foreach ($this->findByTreeNode($treeNode) as $treeNodeOnline) {
            $language[] = $treeNodeOnline->getLanguage();
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(TreeNodeInterface $treeNode)
    {
        $versions = [];
        foreach ($this->findByTreeNode($treeNode) as $treeNodeOnline) {
            $versions[$treeNodeOnline->getLanguage()] = $treeNodeOnline->getVersion();
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(TreeNodeInterface $treeNode, $language)
    {
        $treeNodeOnline = $this->findOneByTreeNodeAndLanguage($treeNode, $language);
        if (!$treeNodeOnline) {
            return null;
        }

        return $treeNodeOnline->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(TreeNodeInterface $treeNode, $language)
    {
        $treeNodeOnline = $this->findOneByTreeNodeAndLanguage($treeNode, $language);
        if (!$treeNodeOnline) {
            return null;
        }

        return $treeNodeOnline->getPublishedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(TreeNodeInterface $treeNode, $language)
    {
        $treeOnline = $this->findOneByTreeNodeAndLanguage($treeNode, $language);
        if (!$treeOnline) {
            return false;
        }

        $version = $this->mediator->getContentDocument($treeNode)->getVersion();

        if ($version === $treeOnline->getVersion()) {
            return false;
        }

        $publishedHash = $treeOnline->getHash();
        $currentHash = $this->nodeHasher->hashNode($treeNode, $version, $language);

        return $publishedHash !== $currentHash;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(TreeNodeInterface $treeNode, $version, $language, $userId, $comment = null)
    {
        $treeNodeOnline = $this->getTreeNodeOnlineRepository()->findOneBy(['treeNode' => $treeNode->getId(), 'language' => $language]);
        if (!$treeNodeOnline) {
            $treeNodeOnline = new TreeNodeOnline();
            $treeNodeOnline
                ->setTreeNode($treeNode);
        }

        $treeNodeOnline
            ->setVersion($version)
            ->setLanguage($language)
            ->setHash($this->nodeHasher->hashNode($treeNode, $version, $language))
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->entityManager->persist($treeNodeOnline);
        $this->entityManager->flush($treeNodeOnline);

        $this->cache = array();

        return $treeNodeOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(TreeNodeInterface $treeNode, $language)
    {
        $treeNodeOnline = $this->getTreeNodeOnlineRepository()->findOneBy(['treeNode' => $treeNode->getId(), 'language' => $language]);

        if ($treeNodeOnline) {
            $this->entityManager->remove($treeNodeOnline);
            $this->entityManager->flush();

            $this->cache = array();
        }
    }
}
