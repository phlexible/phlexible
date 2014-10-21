<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var int
     */
    private $passwordMinLength;

    /**
     * @param RoleHierarchyInterface $roleHierarchy
     * @param array                  $defaults
     * @param int                    $passwordMinLength
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy, array $defaults, $passwordMinLength)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->defaults = $defaults;
        $this->passwordMinLength = $passwordMinLength;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $securityContext = $event->getSecurityContext();
        $token = $securityContext->getToken();
        $user = $token->getUser();
        $roles = array();
        foreach ($this->roleHierarchy->getReachableRoles($token->getRoles()) as $role) {
            $roles[] = $role->getRole();
        }

        $event->getConfig()
            ->set('system.password_min_length', $this->passwordMinLength)
            ->set('user.id', $user->getId())
            ->set('user.username', $user->getUsername())
            ->set('user.email', $user->getEmail())
            ->set('user.firstname', $user->getFirstname() ?: '')
            ->set('user.lastname', $user->getLastname() ?: '')
            ->set('user.properties', $user->getProperties())
            ->set('user.roles', $roles)
            ->set('defaults', $this->defaults);

        foreach ($user->getProperties() as $key => $value) {
            $event->getConfig()->set('user.property.' . $key, $value);
        }
    }
}
