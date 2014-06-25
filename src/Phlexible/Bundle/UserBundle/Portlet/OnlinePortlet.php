<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Online portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlinePortlet extends AbstractPortlet
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param TranslatorInterface  $translator
     * @param UserManagerInterface $userManager
     */
    public function __construct(TranslatorInterface $translator, UserManagerInterface $userManager)
    {
        $this->id        = 'online-portlet';
        $this->title     = $translator->trans('users.who_is_online', array(), 'gui');
        $this->class     = 'Phlexible.users.portlet.Online';
        $this->iconClass = 'p-user-portlet-icon';
//        $this->resource  = 'users';

        $this->userManager = $userManager;
    }

    /**
     * Return portlet data
     *
     * @return array
     */
    public function getData()
    {
        $users = $this->userManager->findLoggedInUsers();

        $data = array();
        foreach ($users as $user) {
            $data[] = array(
                'uid'      => $user->getId(),
                'username' => $user->getUsername(),
                'image'    => '/bundles/users/images/male-black-blonde.png',
            );
        }

        return $data;
    }
}
