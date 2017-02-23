<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Phlexible\Bundle\MessageBundle\Model\SubscriptionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Messages portlet.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagesPortlet extends Portlet
{
    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TranslatorInterface          $translator
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param MessageManagerInterface      $messageManager
     * @param TokenStorageInterface        $tokenStorage
     */
    public function __construct(
        TranslatorInterface $translator,
        SubscriptionManagerInterface $subscriptionManager,
        MessageManagerInterface $messageManager,
        TokenStorageInterface $tokenStorage)
    {
        $this
            ->setId('messages-portlet')
            ->setTitle($translator->trans('messages.messages', [], 'gui'))
            ->setClass('Phlexible.messages.portlet.Messages')
            ->setIconClass('p-message-component-icon')
            ->setRole('ROLE_MESSAGE_SUBSCRIPTIONS');

        $this->subscriptionManager = $subscriptionManager;
        $this->messageManager = $messageManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Return Portlet data.
     *
     * @return array
     */
    public function getData()
    {
        $subscription = $this->subscriptionManager
            ->findOneBy(
                ['userId' => $this->tokenStorage->getToken()->getUser()->getId(), 'handler' => 'portlet']
            );

        if (!$subscription) {
            return [];
        }

        $filter = $subscription->getFilter();

        if (!$filter) {
            return [];
        }

        $messages = $this->messageManager->findByCriteria($filter->getCriteria(), ['createdAt' => 'DESC'], 20);

        $priorityList = $this->messageManager->getPriorityNames();
        $typeList = $this->messageManager->getTypeNames();

        $data = [];
        foreach ($messages as $message) {
            $subject = '';

            $i = 0;
            do {
                $subject .= ($i ? '<wbr />' : '').mb_substr($message->getSubject(), $i, $i + 30, 'UTF-8');
                $i += 30;
            } while ($i <= strlen($message->getSubject()));

            $data[] = [
                'id' => $message->getId(),
                'subject' => $subject,
                'time' => $message->getCreatedAt()->format('U'),
                'priority' => $priorityList[$message->getPriority()],
                'type' => $typeList[$message->getType()],
                'channel' => $message->getChannel(),
            ];
        }

        return $data;
    }
}
