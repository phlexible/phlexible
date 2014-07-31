<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SecurityBundle\SecurityMessage;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Force password change controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/security/passwordchange")
 */
class ForcePasswordChangeController extends Controller
{
    /**
     * Show change password page
     *
     * @param Request $request
     * @return array
     * @Route("", name="security_forcepasswordchange_view")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $forceChangePasswordView = $this->get('phlexible_security.view.force_password_change');

        $user = $this->getUser();

        $csrfProvider = $this->get('form.csrf_provider');
        $csrfToken = $csrfProvider->generateCsrfToken('authenticate');

        return array(
            'baseUrl'        => $request->getBaseUrl(),
            'basePath'       => $request->getBasePath(),
            'componentsPath' => '/bundles',
            'debug'          => $this->container->getParameter('kernel.debug'),
            'theme'          => $user->getProperty('theme', 'default'),
            'language'       => $user->getInterfaceLanguage('en'),
            'appTitle'       => $this->container->getParameter('app.app_title'),
            'appVersion'     => $this->container->getParameter('app.app_version'),
            'appUrl'         => $this->container->getParameter('app.app_url'),
            'projectTitle'   => $this->container->getParameter('app.project_title'),
            'csrfToken'      => $csrfToken,
            'minLength'      => $this->container->getParameter('phlexible_user.password.min_length'),
            'checkUrl'       => $this->generateUrl('security_forcepasswordchange_check'),
            'scripts'        => $forceChangePasswordView->get($request, $this->get('security.context')),
            'noScript'       => $forceChangePasswordView->getNoScript(
                    $request->getBaseUrl(),
                    $this->container->getParameter('app.app_title'),
                    $this->container->getParameter('app.project_title')
                ),
        );
    }

    /**
     * Show change password page
     *
     * @param Request $request
     * @return array
     * @Route("/save", name="security_forcepasswordchange_check")
     */
    public function checkAction(Request $request)
    {
        $newPassword = $request->request->get('new_password');
        $newPasswordRep = $request->request->get('new_password_repeat');

        $user = $this->getUser();

        $passwordCheck = $this->get('phlexible_user.password_checker');

        if ($newPassword != $newPasswordRep) {
            return new ResultResponse(
                false,
                '',
                array(),
                array(
                    array(
                        'id'  => 'new_password_repeat',
                        'msg' => 'passwords_dont_match'
                    )
                )
            );
        } elseif ($result = $passwordCheck->test($newPassword, $user)) {
            return new ResultResponse(
                false,
                '',
                array(),
                array(
                    array(
                        'id'  => 'new_password',
                        'msg' => $result
                    )
                )
            );
        }

        $user->setPlainPassword($newPassword);
        $user->removeProperty('forcePasswordChange');

        $this->get('phlexible_user.user_manager')->updateUser($user);

        $this->get('logger')->notice(
            'User "' . $user->getUsername() . '" set new password due to force password change or expiration.'
        );

        // post cleartext message
        $message = SecurityMessage::create(
            'User "' . $user->getUsername() . '" set new password due to force password change or expiration.',
            null,
            UsersMessage::PRIORITY_LOW
        );
        $this->get('phlexible_message.message_poster')->post($message);

        return new ResultResponse(true, '', array('target' => $this->generateUrl('gui_index')));
    }
}
