<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Options controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OptionsController extends Controller
{
    /**
     * Save details
     *
     * @param Request $request
     *
     * @return ResultResponse
     */
    public function savedetailsAction(Request $request)
    {
        $user = $this->getUser()
            ->setFirstname($request->query->get('firstname'))
            ->setLastname($request->query->get('lastname'))
            ->setEmail($request->query->get('email'));

        $this->get('phlexible_user.user_manager')->updateUser($user);

        return new ResultResponse(true, 'User details saved.');
    }

    /**
     * Save password
     *
     * @param Request $request
     *
     * @return ResultResponse
     */
    public function savepasswordAction(Request $request)
    {
        $user = $this->getUser();

        if ($request->request->has('password')) {
            $user->setPlainPassword($request->query->get('password'));
        }

        $this->get('phlexible_user.user_manager')->updateUser($user);

        return new ResultResponse(true, 'User password saved.');
    }

    /**
     * Save preferences
     *
     * @param Request $request
     *
     * @return ResultResponse
     */
    public function savepreferencesAction(Request $request)
    {
        $user = $this->getUser()
            ->setInterfaceLanguage($request->request->get('interfaceLanguage', 'en'));

        $this->get('phlexible_user.user_manager')->updateUser($user);

        return new ResultResponse(true, 'User preferences saved.');
    }

    /**
     * Save preferences
     *
     * @param Request $request
     *
     * @return ResultResponse
     */
    public function savethemeAction(Request $request)
    {
        $user = $this->getUser()
            ->setProperty('theme', $request->request->get('theme', 'default'));

        $this->get('phlexible_user.user_manager')->updateUser($user);

        return new ResultResponse(true, 'User theme saved.');
    }
}
