<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Digest;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Mailer\Mailer;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\MessageBundle\Model\FilterManagerInterface;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * Digest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Digest
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @param UserManagerInterface
     */
    private $userManager;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @param EntityManager           $entityManager
     * @param FilterManagerInterface  $filterManager
     * @param MessageManagerInterface $messageManager
     * @param MessagePoster           $messageService
     * @param UserManagerInterface    $userManager
     * @param Mailer                  $mailer
     */
    public function __construct(EntityManager $entityManager,
                                FilterManagerInterface $filterManager,
                                MessageManagerInterface $messageManager,
                                MessagePoster $messageService,
                                UserManagerInterface $userManager,
                                Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->filterManager = $filterManager;
        $this->messageManager = $messageManager;
        $this->messageService = $messageService;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
    }

    /**
     * Static send function for use with events
     *
     * @return array
     */
    public function sendDigestMails()
    {
        $subscriptionRepository = $this->entityManager->getRepository('PhlexibleMessageBundle:Subscription');
        $subscriptions = $subscriptionRepository->findByHandler('digest');

        $digests = [];
        foreach ($subscriptions as $subscription) {
            $filter = $this->filterManager->find($subscription->getFilterId());
            if (!$filter) {
                continue;
            }

            $user = $this->userManager->find($subscription->getUserId());
            if (!$user || !$user->getEmail()) {
                continue;
            }

            $lastSend = $subscription->getAttribute('lastSend', null);
            if (!$lastSend) {
                $lastSend = new \DateTime();
                $lastSend = $lastSend->sub(new \DateInterval('P30D'));
            } else {
                $lastSend = new \DateTime($lastSend);
            }

            $criteria = new Criteria([$filter->getCriteria()], Criteria::MODE_AND);
            $criteria->dateFrom($lastSend);
            $messages = $this->messageRepository->findByCriteria($criteria);
            if (!count($messages)) {
                continue;
            }

            if ($this->mailer->sendDigestMail($user, $messages)) {
                $digests[] = ['filter' => $filter->getTitle(), 'to' => $user->getEmail(), 'status' => 'ok'];
                $subscription->setAttribute('lastSend', date('Y-m-d H:i:s'));
                $this->subscriptionRepository->save($subscription);
            } else {
                $digests[] = ['filter' => $filter->getTitle(), 'to' => $user->getEmail(), 'status' => 'failed'];
            }
        }

        if (count($digests)) {
            $message = Message::create(
                count($digests) . ' digest mail(s) sent.',
                'Status: ' . PHP_EOL . print_r($digests, true),
                Message::PRIORITY_NORMAL,
                null,
                'ROLE_MESSAGES',
                'cli'
            );
            $this->messageService->post($message);
        }

        return $digests;
    }
}
