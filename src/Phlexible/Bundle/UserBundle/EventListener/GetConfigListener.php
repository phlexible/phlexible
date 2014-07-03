<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var Acl
     */
    private $defaults;

    /**
     * @var array
     */
    private $acl;

    /**
     * @var int
     */
    private $passwordMinLength;

    /**
     * @param Acl   $acl
     * @param array $defaults
     * @param int   $passwordMinLength
     */
    public function __construct(Acl $acl, array $defaults, $passwordMinLength)
    {
        $this->acl = $acl;
        $this->defaults = $defaults;
        $this->passwordMinLength = $passwordMinLength;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $securityContext = $event->getSecurityContext();
        $user = $securityContext->getToken()->getUser();

        $resources = array();
        $allResources = $this->acl->getResources();
        foreach ($allResources as $resource) {
            if ($securityContext->isGranted($resource)) {
                $resources[] = $resource;
            }
        }
        $roles = $user->getRoles();

        $event->getConfig()
            ->set('system.password_min_length', $this->passwordMinLength)
            ->set('user.id', $user->getId())
            ->set('user.details.username', $user->getUsername())
            ->set('user.details.email', $user->getEmail())
            ->set('user.details.firstname', $user->getFirstname() ?: '')
            ->set('user.details.lastname', $user->getLastname() ?: '')
            ->set('user.resources', $resources)
            ->set('user.roles', $roles)
            ->set('defaults', $this->defaults);

        foreach ($user->getProperties() as $key => $value) {
            $event->getConfig()->set('user.property.' . $key, $value);
        }
    }
}
