<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFactory implements TreeFactoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementHistoryManagerInterface
     */
    private $historyManager;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Connection                     $connection
     * @param ElementHistoryManagerInterface $historyManager
     * @param StateManagerInterface          $stateManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(Connection $connection, ElementHistoryManagerInterface $historyManager, StateManagerInterface $stateManager, EventDispatcherInterface $dispatcher)
    {
        $this->connection = $connection;
        $this->historyManager = $historyManager;
        $this->stateManager = $stateManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siterootId)
    {
        return new Tree($siterootId, $this->connection, $this->historyManager, $this->stateManager, $this->dispatcher);
    }
}
