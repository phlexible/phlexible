<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ViabilityManagerInterface;

/**
 * Viability manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViabilityManager implements ViabilityManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getAllowedParentIds(Elementtype $elementtype)
    {
        $connection = $this->entityManager->getConnection();
        $qb = $connection->createQueryBuilder();

        $qb
            ->select('v.apply_under_id')
            ->from('elementtype_apply', 'v')
            ->where($qb->expr()->eq('v.elementtype_id', $elementtype->getId()));

        return array_column($connection->fetchAll($qb->getSQL()), 'apply_under_id');
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getAllowedChildrenIds(Elementtype $elementtype)
    {
        $connection = $this->entityManager->getConnection();
        $qb = $connection->createQueryBuilder();

        $qb
            ->select('v.elementtype_id')
            ->from('elementtype_apply', 'v')
            ->where($qb->expr()->eq('v.apply_under_id', $elementtype->getId()));

        return array_column($connection->fetchAll($qb->getSQL()), 'elementtype_id');
    }

    /**
     * Update viability
     *
     * @param Elementtype $elementtype
     * @param array       $parentIds
     *
     * @return $this
     */
    public function updateViability(Elementtype $elementtype, array $parentIds)
    {
        $connection = $this->entityManager->getConnection();

        $connection->delete(
            'elementtype_apply',
            array(
                'elementtype_id' => $elementtype->getId()
            )
        );

        foreach ($parentIds as $parentId) {
            $connection->insert(
                'elementtype_apply',
                array(
                    'elementtype_id' => $elementtype->getId(),
                    'apply_under_id' => $parentId,
                )
            );
        }
    }
}
