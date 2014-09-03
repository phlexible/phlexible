<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
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
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @param Connection                     $connection
     * @param EventDispatcherInterface       $dispatcher
     * @param ElementHistoryManagerInterface $historyManager
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher,
        ElementHistoryManagerInterface $historyManager)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(Teaser $teaser, $language)
    {
        $publishedVersions = $this->getPublishedVersions($teaser);

        return isset($publishedVersions[$language]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(Teaser $teaser)
    {
        return array_keys($this->getPublishedVersions($teaser));
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(Teaser $teaser)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(array('t_o.language', 't_o.version'))
            ->from('tree_online', 't_o')
            ->where($qb->expr()->eq('t_o.tree_id', $teaser->getId()));

        $statement = $this->connection->executeQuery($qb->getSQL());

        $versions = array();
        while ($row = $statement->fetch()) {
            $versions[$row['language']] = (int) $row['version'];
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(Teaser $teaser, $language)
    {
        $publishedVersions = $this->getPublishedVersions($teaser);
        if (!isset($publishedVersions[$language])) {
            return null;
        }

        return $publishedVersions[$language];
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishInfo(Teaser $teaser, $language)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('t_o.*')
            ->from('tree_online', 't_o')
            ->where($qb->expr()->eq('t_o.tree_id', $teaser->getId()))
            ->andWhere($qb->expr()->eq('t_o.language', $qb->expr()->literal($language)));

        return $this->connection->fetchAssoc($qb->getSQL());
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(Teaser $teaser, $language)
    {
        // TODO: implement

        return true;
    }
}
