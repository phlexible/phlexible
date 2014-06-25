<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Provider;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Group provider
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class GroupProvider implements ProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $groupRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->groupRepository = $entityManager->getRepository('PhlexibleUserBundle:Group');
    }

    /**
     * Return object name
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    public function getName($objectType, $objectId)
    {
        return $this->groupRepository->find($objectId)->getName();
    }

    /**
     * Return objects
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset)
    {
        $groups = $this->groupRepository->findAll();

        $data = array();
        foreach ($groups as $group) {
            $data[] = array(
                'type'        => 'group',
                'object_type' => 'gid',
                'object_id'   => $group->getId(),
                'label'       => $group->getName(),
            );
        }

        return array(
            'count' => count($data),
            'data'  => $data,
        );
    }
}
