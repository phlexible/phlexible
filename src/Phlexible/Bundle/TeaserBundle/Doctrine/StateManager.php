<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Entity\TeaserOnline;
use Phlexible\Bundle\TeaserBundle\Model\StateManagerInterface;
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
     * @var EntityRepository
     */
    private $teaserOnlineRepository;

    /**
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->historyManager = $historyManager;
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
        return $this->getTeaserOnlineRepository()->findBy(array('teaser' => $teaser));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByTeaserAndLanguage(Teaser $teaser, $language)
    {
        return $this->getTeaserOnlineRepository()->findOneBy(array('teaser' => $teaser, 'language' => $language));
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
        $language = array();
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
        $versions = array();
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

        return $teaserOnline->getLanguage();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        // TODO: implement

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Teaser $teaser, $version, $language, $userId, $comment = null)
    {
        $teaserOnline = $this->getTeaserOnlineRepository()->findOneBy(array('teaser' => $teaser, 'language' => $language));
        if (!$teaserOnline) {
            $teaserOnline = new TeaserOnline();
            $teaserOnline
                ->setTeaser($teaser);
        }

        $teaserOnline
            ->setLanguage($language)
            ->setVersion($version)
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->entityManager->persist($teaserOnline);
        $this->entityManager->flush($teaserOnline);

        return $teaserOnline;
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(Teaser $teaser, $language)
    {
        $teaserOnline = $this->getTeaserOnlineRepository()->findOneBy(array('teaser' => $teaser, 'language' => $language));

        if ($teaserOnline) {
            $this->entityManager->remove($teaserOnline);
            $this->entityManager->flush();
        }
    }
}
