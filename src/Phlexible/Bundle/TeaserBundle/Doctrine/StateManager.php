<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Entity\TeaserOnline;
use Phlexible\Bundle\TeaserBundle\Mediator\Mediator;
use Phlexible\Bundle\TeaserBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserHasher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * State manager
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @var Mediator
     */
    private $mediator;

    /**
     * @var TeaserHasher
     */
    private $teaserHasher;

    /**
     * @var EntityRepository
     */
    private $teaserOnlineRepository;

    private $cache = array();

    /**
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param Mediator                       $mediator
     * @param TeaserHasher                   $teaserHasher
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        Mediator $mediator,
        TeaserHasher $teaserHasher,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->historyManager = $historyManager;
        $this->mediator = $mediator;
        $this->teaserHasher = $teaserHasher;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getTeaserOnlineRepository()
    {
        if (null === $this->teaserOnlineRepository) {
            $this->teaserOnlineRepository = $this->entityManager->getRepository('PhlexibleTeaserBundle:TeaserOnline');
        }

        return $this->teaserOnlineRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findByTeaser(Teaser $teaser)
    {
        $id = $teaser->getId();

        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getTeaserOnlineRepository()->findBy(['teaser' => $teaser->getId()]);
        }

        return $this->cache[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTeaserAndLanguage(Teaser $teaser, $language)
    {
        $id = $teaser->getId() . '_' . $language;

        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getTeaserOnlineRepository()->findOneBy(['teaser' => $teaser->getId(), 'language' => $language]);
        }

        return $this->cache[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(Teaser $teaser, $language)
    {
        return $this->findOneByTeaserAndLanguage($teaser, $language) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(Teaser $teaser)
    {
        $language = [];
        foreach ($this->findByTeaser($teaser) as $teaserOnline) {
            $language[] = $teaserOnline->getLanguage();
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(Teaser $teaser)
    {
        $versions = [];
        foreach ($this->findByTeaser($teaser) as $teaserOnline) {
            $versions[$teaserOnline->getLanguage()] = $teaserOnline->getVersion();
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(Teaser $teaser, $language)
    {
        $teaserOnline = $this->findOneByTeaserAndLanguage($teaser, $language);
        if (!$teaserOnline) {
            return null;
        }

        return $teaserOnline->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        $teaserOnline = $this->findOneByTeaserAndLanguage($teaser, $language);
        if (!$teaserOnline) {
            return false;
        }

        $version = $this->mediator->getVersionedObject($teaser)->getVersion();

        if ($version === $teaserOnline->getVersion()) {
            return false;
        }

        $publishedHash = $teaserOnline->getHash();
        $currentHash = $this->teaserHasher->hashTeaser($teaser, $version, $language);

        return $publishedHash !== $currentHash;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Teaser $teaser, $version, $language, $userId, $comment = null)
    {
        $teaserOnline = $this->getTeaserOnlineRepository()->findOneBy(['teaser' => $teaser, 'language' => $language]);
        if (!$teaserOnline) {
            $teaserOnline = new TeaserOnline();
            $teaserOnline
                ->setTeaser($teaser);
        }

        $teaserOnline
            ->setLanguage($language)
            ->setVersion($version)
            ->setHash($this->teaserHasher->hashTeaser($teaser, $version, $language))
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->entityManager->persist($teaserOnline);
        $this->entityManager->flush($teaserOnline);

        $this->cache = array();

        return $teaserOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(Teaser $teaser, $language)
    {
        $teaserOnline = $this->getTeaserOnlineRepository()->findOneBy(['teaser' => $teaser, 'language' => $language]);

        if ($teaserOnline) {
            $this->entityManager->remove($teaserOnline);
            $this->entityManager->flush();

            $this->cache = array();
        }
    }
}
