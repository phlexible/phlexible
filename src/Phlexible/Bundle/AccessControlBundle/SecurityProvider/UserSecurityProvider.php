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

use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityProviderInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityResolverInterface;

/**
 * User security provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserSecurityProvider implements SecurityProviderInterface, SecurityResolverInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @param UserManagerInterface $userManager
     * @param string               $userClass
     */
    public function __construct(UserManagerInterface $userManager, $userClass)
    {
        $this->userManager = $userManager;
        $this->userClass = $userClass;
    }

    /**
     * Return security name.
     *
     * @param string $securityType
     * @param string $securityId
     *
     * @return string
     */
    public function resolveName($securityType, $securityId)
    {
        if ($securityType !== $this->userClass) {
            return null;
        }

        return $this->userManager->find($securityId)->getDisplayName();
    }

    /**
     * Return users.
     *
     * @param string $query
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getAll($query, $limit, $offset)
    {
        // TODO: user query
        $users = $this->userManager->findBy(array(), array('lastname' => 'ASC'), $limit, $offset);

        $data = array();
        foreach ($users as $user) {
            $data[] = array(
                'securityType' => get_class($user),
                'securityId' => $user->getId(),
                'securityName' => $user->getDisplayName(),
            );
        }

        return array(
            'total' => $this->userManager->countAll(),
            'data' => $data,
        );
    }
}
