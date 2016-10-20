<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Subscriptions controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/messages/subscriptions")
 */
class SubscriptionsController extends Controller
{
    /**
     * List subscriptions
     *
     * @return JsonResponse
     * @Route("", name="messages_subscriptions")
     */
    public function listAction()
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');

        $subscriptions = [];

        foreach ($subscriptionManager->findAll() as $subscription) {
            $subscriptions[] = [
                'id'       => $subscription->getId(),
                'filterId' => $subscription->getFilter()->getId(),
                'filter'   => $subscription->getFilter()->getTitle(),
                'handler'  => $subscription->getHandler(),
            ];
        }

        return new JsonResponse($subscriptions);
    }

    /**
     * Create subscription
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="messages_subscription_create")
     */
    public function createAction(Request $request)
    {
        $filterId = $request->get('filter');
        $handler = $request->get('handler');

        $subscriptionManager = $this->get('phlexible_message.subscription_manager');
        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->find($filterId);

        $subscription = $subscriptionManager->create()
            ->setUserId($this->getUser()->getId())
            ->setFilter($filter)
            ->setHandler($handler);

        $subscriptionManager->updateSubscription($subscription);

        return new ResultResponse(true, 'Subscription created.');
    }

    /**
     * Delete subscription
     *
     * @param string $id
     *
     * @return ResultResponse
     * @Route("/delete/{id}", name="messages_subscription_delete")
     */
    public function deleteAction($id)
    {
        $subscriptionManager = $this->get('phlexible_message.subscription_manager');

        $subscription = $subscriptionManager->find($id);
        $subscriptionManager->deleteSubscription($subscription);

        return new ResultResponse(true, 'Subscription deleted.');
    }
}
