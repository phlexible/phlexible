<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure value sequence
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureValueSequence
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var int
     */
    private $id;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return int
     */
    public function next()
    {
        if (null === $this->id) {
            $conn = $this->entityManager->getConnection();
            $qb = $conn->createQueryBuilder();
            $qb
                ->select('data_id')
                ->from('element_structure_value', 'esv')
                ->orderBy('data_id', 'desc')
                ->setMaxResults(1);
            $this->id = (int) $conn->fetchColumn($qb->getSQL());
        }

        return ++$this->id;
    }
}
