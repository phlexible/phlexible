<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\ElementStructure;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureManager implements ElementStructureManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementStructureLoader
     */
    private $elementStructureLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @param EntityManager            $entityManager
     * @param ElementStructureLoader   $elementStructureLoader
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        ElementStructureLoader $elementStructureLoader,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->elementStructureLoader = $elementStructureLoader;
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function find(ElementVersion $elementVersion, $language)
    {
        return $this->elementStructureLoader->load($elementVersion, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementStructure(ElementStructure $elementStructure, $flush = true)
    {
    }
}
