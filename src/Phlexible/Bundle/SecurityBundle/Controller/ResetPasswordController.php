<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SecurityBundle\SecurityMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reset password controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/security/reset")
 */
class ResetPasswordController extends Controller
{
    /**
     * Show validate email page
     *
     * @param Request $request
     *
     * @return array
     * @Route("/validate", name="security_reset_validate_view")
     * @Template
     */
    public function validateViewAction(Request $request)
    {
        $validateEmailView = $this->get('phlexible_security.view.validate_email');

        $csrfProvider = $this->get('form.csrf_provider');
        $csrfToken = $csrfProvider->generateCsrfToken('authenticate');

        return array(
            'baseUrl'        => $request->getBaseUrl(),
            'basePath'       => $request->getBasePath(),
            'componentsPath' => '/bundles',
            'debug'          => $this->container->getParameter('kernel.debug'),
            'theme'          => 'default', //$currentUser->getOption('theme', 'default'),
            'language'       => 'en', //$currentUser->getInterfaceLanguage(),
            'appTitle'       => $this->container->getParameter('app.app_title'),
            'appVersion'     => $this->container->getParameter('app.app_version'),
            'appUrl'         => $this->container->getParameter('app.app_url'),
            'projectTitle'   => $this->container->getParameter('app.project_title'),
            'scripts'        => $validateEmailView->get($request, $this->get('security.context')),
            'csrfToken'      => $csrfToken,
            'validateUrl'    => $this->generateUrl('security_reset_validate_check'),
            'loginUrl'       => $this->generateUrl('security_login'),
            'noScript'       => $validateEmailView->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );
    }

    /**
     * Validate and send email
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/validate/check", name="security_reset_validate_check")
     */
    public function validateCheckAction(Request $request)
    {
        $email = $request->request->get('email');

        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->findOneBy(array('email' => $email));

        if ($user) {
            $token = Uuid::generate();
            $user->setPasswordToken($token);
            $userManager->updateUser($user);

            $validateUrl = $this->generateUrl('security_reset_set', array('token' => $token));

            $mailer = $this->get('phlexible_security.mailer');
            $mailer->sendValidateEmailMessage($user, $validateUrl);

            return new ResultResponse(true);
        } else {
            return new ResultResponse(false);
        }
    }

    /**
     * Show reset password page
     *
     * @param Request $request
     *
     * @return Response
     * @Route("", name="security_reset_view")
     * @Template
     */
    public function resetViewAction(Request $request)
    {
        $token = $request->query->get('token');

        $user = $this->get('phlexible_user.user_manager')->findOneBy(array('passwordToken' => $token));
        if (!$user) {
            return new Response('Token not found.', 403);
        }

        $resetPasswordView = $this->get('phlexible_security.view.reset_password');

        $csrfProvider = $this->get('form.csrf_provider');
        $csrfToken = $csrfProvider->generateCsrfToken('authenticate');

        return array(
            'baseUrl'        => $request->getBaseUrl(),
            'basePath'       => $request->getBasePath(),
            'componentsPath' => '/bundles',
            'debug'          => $this->container->getParameter('kernel.debug'),
            'theme'          => 'default', //$currentUser->getOption('theme', 'default'),
            'language'       => 'en', //$currentUser->getInterfaceLanguage(),
            'appTitle'       => $this->container->getParameter('app.app_title'),
            'appVersion'     => $this->container->getParameter('app.app_version'),
            'appUrl'         => $this->container->getParameter('app.app_url'),
            'projectTitle'   => $this->container->getParameter('app.project_title'),
            'csrfToken'      => $csrfToken,
            'token'          => $token,
            'loginUrl'       => $this->generateUrl('security_login'),
            'setUrl'         => $this->generateUrl('security_reset_check'),
            'minLength'      => $this->container->getParameter('phlexible_user.password.min_length'),
            'scripts'        => $resetPasswordView->get($request, $this->get('security.context')),
            'noScript'       => $resetPasswordView->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );
    }

    /**
     * Set new password
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/check", name="security_reset_check")
     */
    public function setAction(Request $request)
    {
        $token           = $request->request->get('token');
        $newPassword     = $request->request->get('new_password');
        $newPasswordRep  = $request->request->get('new_password_repeat');

        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->findOneBy(array('passwordToken' => $token));
        if (!$user) {
            return new Response('Token not found.', 403);
        }

        $passwordCheck = $this->get('phlexible_user.password_checker');
        $translator  = $this->get('translator');

        if ($newPassword != $newPasswordRep) {
            return new ResultResponse(false, array(), array(
                array(
                    'id' => 'new_password_repeat',
                    'msg' => $translator->trans('phlexible_security.passwords_dont_match')
                )
            ));
        } elseif ($result = $passwordCheck->test($newPassword, $user)) {
            return new ResultResponse(false, array(), array(
                array(
                    'id' => 'new_password',
                    'msg' => $result
                )
            ));
        }

        $user
            ->setPlainPassword($newPassword)
            ->setPasswordToken(null);
        $userManager->updateUser($user);

        $this->get('logger')->notice('User "'.$user->getUsername().'" set new password.');

        // post cleartext message
        $message = SecurityMessage::create(
            'User "'.$user->getUsername().'" set new password.',
            null,
            SecurityMessage::PRIORITY_LOW
        );
        $this->get('phlexible_message.message_poster')->post($message);

        return new ResultResponse(true);
    }
}
