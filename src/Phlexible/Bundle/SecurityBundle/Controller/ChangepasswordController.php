<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Change password controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChangepasswordController extends Controller
{
    /**
     * Show change password page
     */
    public function indexAction()
    {
        $container = $this->getContainer();
        /* @var $twig Twig_Environment */
        $twig = $container->get('twig');
        $t9n = $container->get('translator');
        $viewChangePassword = $container->get('usersViewChangePassword');

        $request = $this->getRequest();
        $currentUser = $this->getUser();

        $twig->getLoader()->addPath(__DIR__ . '/../Resources/views/');

        $loginSession = new \Zend_Session_Namespace('login');
        $csrfToken = $loginSession->csrfToken = Uuid::generate();

        $output = $twig->render(
            '@users/ChangePassword/change_password.html.twig',
            array(
                'baseUrl'      => $request->getBaseUrl(),
                'basePath'     => $request->getBasePath(),
                'extPath'      => $container->getParameter('gui.ext_url'),
                't9n'          => $t9n->frame,
                'debug'        => $container->getParameter('kernel.debug'),
                'theme'        => $currentUser->getOption('theme'),
                'language'     => $currentUser->getInterfaceLanguage(),
                'appTitle'     => $container->getParameter('app.app_title'),
                'appVersion'   => $container->getParameter('app.app_version'),
                'appUrl'       => $container->getParameter('app.app_url'),
                'projectTitle' => $container->getParameter('app.project_title'),
                'scripts'      => $viewChangePassword->get($this->getRequest(), $this->getSecurityContext()),
                'noScript'     => $viewChangePassword->getNoScript(
                        $request->getBaseUrl(),
                        $container->getParameter('app.app_title'),
                        $container->getParameter('app.project_title')
                    ),
                'csrfToken'    => $csrfToken
            )
        );

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->appendBody($output);
    }

    public function saveAction()
    {
        $currentPassword = $this->getParam('current_password');
        $newPassword = $this->getParam('new_password');
        $newPasswordRep = $this->getParam('new_password_repeat');

        $user = $this->getUser();

        $container = $this->getContainer();
        $passwordCheck = $container->get('usersPasswordCheck');
        $t9n = $this->getContainer()->t9n;
        $page = $t9n->users;

        if (!$user->checkPassword($currentPassword)) {
            $this->_response->setResult(
                false,
                null,
                '',
                array(),
                array(
                    array(
                        'id'  => 'current_password',
                        'msg' => $page->current_password_wrong
                    )
                )
            );

            return;
        } elseif ($newPassword != $newPasswordRep) {
            $this->_response->setResult(
                false,
                null,
                '',
                array(),
                array(
                    array(
                        'id'  => 'new_password_repeat',
                        'msg' => $page->passwords_dont_match
                    )
                )
            );

            return;
        } elseif ($currentPassword == $newPassword) {
            $this->_response->setResult(
                false,
                null,
                '',
                array(),
                array(
                    array(
                        'id'  => 'new_password',
                        'msg' => $page->password_are_the_same
                    )
                )
            );

            return;
        } elseif ($result = $passwordCheck->test($newPassword, $user)) {
            $this->_response->setResult(
                false,
                null,
                '',
                array(),
                array(
                    array(
                        'id'  => 'new_password',
                        'msg' => $result
                    )
                )
            );

            return;
        }

        $user->setPassword($newPassword);
        $user->setFlag(User::FLAG_FORCE_PASSWORD_CHANGE, false);
        $user->save();

        $this->getContainer()->get('logger')->notice(
            'User "' . $user->getUsername() . '" set new password due to force password change or expiration.'
        );

        // post cleartext message
        $message = UsersMessage::create(
            'User "' . $user->getUsername() . '" set new password due to force password change or expiration.',
            null,
            UsersMessage::PRIORITY_LOW
        );
        $message->post();

        $this->_response->setResult(true, null, '');
    }
}
