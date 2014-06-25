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
class ResetController extends Controller
{
    /**
     * Show validate email page
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/validate/view", name="security_reset_validate_view")
     * @Template
     */
    public function validateviewAction(Request $request)
    {
        $twig = $this->get('twig');
        $translator = $this->get('translator');
        $viewValidate = $this->get('security.view.validate');

        $loginSession = new \Zend_Session_Namespace('login');
        $csrfToken = $loginSession->csrfToken = Uuid::generate();

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
            'projectTitle'   => $this->container->getParameter(':app.project_title'),
            'scripts'        => $viewValidate->get($request, $this->get('security.context')),
            'csrfToken'      => $csrfToken,
            'validateUrl'    => $this->generateUrl('security_reset_validate'),
            'loginUrl'       => $this->generateUrl('security_login'),
            'noScript'       => $viewValidate->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );

        return new Response($content);
    }

    /**
     * Validate and send email
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/validate", name="security_reset_validate")
     */
    public function validateAction(Request $request)
    {
        $email = $request->query->get('email');

        $userRepository = $this->get('users.repository');
        $user  = $userRepository->findOneBy(array('email' => $email));

        if ($user) {
            $token = Uuid::generate();
            $user->setPasswordToken($token);
            $userRepository->save($user);

            $validateUrl = $this->generateUrl('security_reset_set', array('token' => $token));

            $mailer = $this->get('security.mailer');
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
     * @Route("/set/view", name="security_reset_set_view")
     */
    public function setviewAction(Request $request)
    {
        $token = $request->query->get('token');

        $user = $this->get('users.repository')->findOneBy(array('password_token' => $token));
        if (!$user) {
            return new Response('Token not found.', 403);
        }

        $twig = $this->get('twig');
        $viewReset = $this->get('security.view.reset');

        $csrfToken = Uuid::generate();
        $request->getSession()->set('login.csrf', $csrfToken);

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
            'projectTitle'   => $this->container->getParameter(':app.project_title'),
            'scripts'        => $viewReset->get($request, $this->get('ecurity_context')),
            'csrfToken'      => $csrfToken,
            'token'          => $token,
            'loginUrl'       => $this->generateUrl('security_login'),
            'setUrl'         => $this->generateUrl('security_reset_set'),
            'minLength'      => $this->container->getParameter('users.password.min_length'),
            'noScript'       => $viewReset->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );

        return new Response($content);
    }

    /**
     * Set new password
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/set", name="security_reset_set")
     */
    public function setAction(Request $request)
    {
        $token           = $request->query->get('token');
        $newPassword     = $request->query->get('new_password');
        $newPasswordRep  = $request->query->get('new_password_repeat');

        $userRepository = $this->get('users.repository');
        $user = $userRepository->findOneBy(array('password_token' => $token));
        if (!$user) {
            return new Response('Token not found.', 403);
        }

        $passwordCheck = $this->get('users.password.checker');
        $translator  = $this->get('translator');

        if ($newPassword != $newPasswordRep) {
            return new ResultResponse(false, array(), array(
                array(
                    'id' => 'new_password_repeat',
                    'msg' => $translator->trans('security.passwords_dont_match')
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
        $userRepository->save($user);

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
