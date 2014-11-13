<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $user = $event->getSecurityContext()->getToken()->getUser();
        $config = $event->getConfig();

        $defaultPortlets = [
            'online-portlet'   => [
                'mode' => 'opened',
                'col'  => 1,
                'pos'  => 1
            ],
            'problems-portlet' => [
                'mode' => 'opened',
                'col'  => 0,
                'pos'  => 2
            ]
        ];

        if ($user->getProperty('portlets')) {
            $portlets = json_decode($user->getProperty('portlets'), true);
        } else {
            $portlets = $defaultPortlets;
        }

        $config->set('user.portlets', $portlets);
    }
}
