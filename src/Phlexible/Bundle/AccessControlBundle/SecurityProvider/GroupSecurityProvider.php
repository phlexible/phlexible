<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\SecurityProvider;

use Phlexible\Bundle\UserBundle\Model\GroupManagerInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityProviderInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityResolverInterface;

/**
 * Group security provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GroupSecurityProvider implements SecurityProviderInterface, SecurityResolverInterface
{
    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    /**
     * @param GroupManagerInterface $groupManager
     */
    public function __construct(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveName($securityType, $securityId)
    {
        if ($securityType !== 'Phlexible\Bundle\UserBundle\Entity\Group') {
            return null;
        }

        return $this->groupManager->find($securityId)->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($query, $limit, $offset)
    {
        $groups = $this->groupManager->findBy(array(), array('name' => 'ASC'), $limit, $offset);

        $data = array();
        foreach ($groups as $group) {
            $data[] = array(
                'securityType' => get_class($group),
                'securityId'   => $group->getId(),
                'securityName' => $group->getName(),
            );
        }

        return array(
            'count' => count($data), // TODO: countBy()
            'data'  => $data,
        );
    }
}
