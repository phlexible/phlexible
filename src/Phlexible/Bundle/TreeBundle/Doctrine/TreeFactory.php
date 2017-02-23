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
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database tree.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFactory implements TreeFactoryInterface
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
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManager                  $entityManager
     * @param ElementHistoryManagerInterface $historyManager
     * @param StateManagerInterface          $stateManager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementHistoryManagerInterface $historyManager,
        StateManagerInterface $stateManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->historyManager = $historyManager;
        $this->stateManager = $stateManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($siterootId)
    {
        return new Tree($siterootId, $this->entityManager, $this->historyManager, $this->stateManager, $this->dispatcher);
    }
}
