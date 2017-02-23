<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Get config listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $config = $event->getConfig();

        $defaultPortlets = [
            'online-portlet' => [
                'mode' => 'opened',
                'col' => 1,
                'pos' => 1,
            ],
            'problems-portlet' => [
                'mode' => 'opened',
                'col' => 0,
                'pos' => 2,
            ],
        ];

        if ($user->getProperty('portlets')) {
            $portlets = json_decode($user->getProperty('portlets'), true);
        } else {
            $portlets = $defaultPortlets;
        }

        $config->set('user.portlets', $portlets);
    }
}
