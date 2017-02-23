<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Poll controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/poll")
 */
class PollController extends Controller
{
    /**
     * Poll Action.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="gui_poll")
     */
    public function indexAction(Request $request)
    {
        $messages = [];

        $data = [];
        foreach ($this->get('phlexible_dashboard.portlets') as $portlet) {
            $data[$portlet->getId()] = $portlet->getData();
        }

        $message = new \stdClass();
        $message->type = 'start';
        $message->event = 'update';
        $message->uid = $this->getUser()->getId();
        $message->msg = null;
        $message->data = $data;
        $message->objectID = null;
        $message->ts = date('Y-m-d H:i:s');

        $messages[] = (array) $message;

        $request->getSession()->set('lastPoll', date('Y-m-d H:i:s'));

        /*
        $lastMessages = MWF_Core_Messages_Message_Query::getByFilter(
            array(
                array(
                    array(
                        'key'   => MWF_Core_Messages_Filter::CRITERIUM_START_DATE,
                        'value' => $pollSession->lastPoll
                    )
                )
            ),
            $this->getSecurityContext()->getUser()->getId(),
            5
        );

        foreach ($lastMessages as $lastMessage) {
            try {
                $user = MWF_Core_Users_User_Peer::getByUserID($lastMessage['create_uid']);
            } catch (\Exception $e) {
                $user = MWF_Core_Users_User_Peer::getSystemUser();
            }

            $message = new MWF_Core_Messages_Frontend_Message();
            $message->type = 'message';
            $message->event = 'message';
            $message->uid = $lastMessage['create_uid'];
            $message->msg = $lastMessage['subject'] . ' [' . $user->getUsername() . ']';
            $message->data = array();
            $message->objectID = null;
            $message->ts = $lastMessage['created_at'];

            if ($lastMessage['created_at'] > $pollSession->lastPoll) {
                $pollSession->lastPoll = $lastMessage['created_at'];
            }

            $messages[] = $message;
        }
        */

        return new JsonResponse($messages);
    }
}
