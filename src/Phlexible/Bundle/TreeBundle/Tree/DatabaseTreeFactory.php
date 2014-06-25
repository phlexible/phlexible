<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Component\Database\ConnectionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseTreeFactory implements TreeFactoryInterface
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TreeHistory
     */
    private $history;

    /**
     * @param ConnectionManager        $dbPool
     * @param EventDispatcherInterface $dispatcher
     * @param TreeHistory              $history
     */
    public function __construct(ConnectionManager $dbPool, EventDispatcherInterface $dispatcher, TreeHistory $history)
    {
        $this->db = $dbPool->default;
        $this->dispatcher = $dispatcher;
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siterootId)
    {
        return new DatabaseTree($siterootId, $this->db, $this->dispatcher, $this->history);
    }
}
