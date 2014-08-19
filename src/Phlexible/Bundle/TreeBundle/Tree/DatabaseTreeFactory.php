<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseTreeFactory implements TreeFactoryInterface
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
     * @var TreeHistory
     */
    private $history;

    /**
     * @param Connection               $connection
     * @param EventDispatcherInterface $dispatcher
     * @param TreeHistory              $history
     */
    public function __construct(Connection $connection, EventDispatcherInterface $dispatcher, TreeHistory $history)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siterootId)
    {
        return new DatabaseTree($siterootId, $this->connection, $this->dispatcher, $this->history);
    }
}
