<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\SecurityProvider;

use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityProviderInterface;
use Phlexible\Component\AccessControl\SecurityProvider\SecurityResolverInterface;

/**
 * User security provider
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
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Return security name
     *
     * @param string $securityType
     * @param string $securityId
     *
     * @return string
     */
    public function resolveName($securityType, $securityId)
    {
        if ($securityType !== 'Phlexible\Bundle\UserBundle\Entity\User') {
            return null;
        }

        return $this->userManager->find($securityId)->getDisplayName();
    }

    /**
     * Return users
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
                'securityId'   => $user->getId(),
                'securityName' => $user->getDisplayName(),
            );
        }

        return array(
            'total' => $this->userManager->countAll(),
            'data'  => $data,
        );
    }
}
