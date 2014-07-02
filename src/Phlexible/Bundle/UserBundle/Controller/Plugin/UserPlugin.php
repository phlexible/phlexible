<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller\Plugin;

use Phlexible\Bundle\UserBundle\User;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Zend_Controller_Plugin_Abstract as AbstractPlugin;
use Zend_Controller_Request_Abstract as Request;

/**
 * User frontcontroller plugin
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserPlugin extends AbstractPlugin
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var int
     */
    private $expireDays;

    /**
     * @param SecurityContextInterface $securityContext
     * @param int                      $expireDays
     */
    public function __construct(SecurityContextInterface $securityContext, $expireDays)
    {
        $this->securityContext = $securityContext;
        $this->expireDays = $expireDays;
    }

    /**
     * {@inheritdoc}
     */
    public function preDispatch(Request $request)
    {
        // TODO: als listener
        return;
        if ($this->securityContext->isAuthenticated() &&
            $request->isGet() &&
            !$request->isXmlHttpRequest() &&
            ($request->getModuleName() == 'frame' || $request->getModuleName() == ''))
        {
            $user = $this->securityContext->getUser();
            $needsChange = false;

            if ($user->getFlag(User::FLAG_FORCE_PASSWORD_CHANGE))
            {
                \MWF_Log::notice('User "'.$user->getUsername().'" redirected to change password site, force password change flag is active.');

                // post cleartext message
                $message = UsersMessage::create(
                    sprintf('User "%s" redirected to change password site, force password change flag is active.', $user->getUsername()),
                    null,
                    UsersMessage::PRIORITY_LOW
                )->post();

                $needsChange = true;
            }
            elseif ($this->expireDays && $user->getPasswordChangeDays() > $this->expireDays && !$user->getFlag(User::FLAG_NO_PASSWORD_CHANGE))
            {
                \MWF_Log::notice('User "'.$user->getUsername().'" redirected to change password site, password is expired.');

                // post cleartext message
                $message = UsersMessage::create(
                    sprintf('User "%s" redirected to change password site, password is expired.', $user->getUsername()),
                    null,
                    UsersMessage::PRIORITY_LOW
                )->post();

                $needsChange = true;
            }

            if($needsChange)
            {
                $request
                    ->setModuleName('users')
                    ->setControllerName('changepassword')
                    ->setActionName('index');
            }
        }
    }
}
