<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;

/**
 * Audit listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AuditListener implements EventSubscriber
{
    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @param MessagePoster $messagePoster
     */
    public function __construct(MessagePoster $messagePoster)
    {
        $this->messagePoster = $messagePoster;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'onFlush',
        );
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!($entity instanceof Message)) {
                $subject = 'Entity ' . get_class($entity) . ' created.';
                $changeSet = $unitOfWork->getEntityChangeSet($entity);
                $body = array();
                foreach ($changeSet as $field => $changes) {
                    $to = $changes[1];
                    if ($to instanceof \DateTime) {
                        $to = $to->format('Y-m-d H:i:s');
                    } elseif (is_object($to)) {
                        $to = '(object)';
                    } elseif (!is_scalar($to)) {
                        $to = var_export($to, true);
                    }
                    $body[] = $field . ': ' . $to;
                }
                $body = implode(PHP_EOL, $body);
                $message = Message::create($subject, $body, 1, 2);
                $this->messagePoster->post($message);
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!($entity instanceof Message)) {
                $subject = 'Entity ' . get_class($entity) . ' updated.';
                $changeSet = $unitOfWork->getEntityChangeSet($entity);
                $body = array();
                foreach ($changeSet as $field => $changes) {
                    $from = $changes[0];
                    $to = $changes[1];
                    if ($changes[0] !== $changes[1]) {
                        if ($from instanceof \DateTime) {
                            $from = $from->format('Y-m-d H:i:s');
                        } elseif (is_object($from)) {
                            $from = '(object)';
                        } elseif (!is_scalar($from)) {
                            $from = var_export($from, true);
                        }
                        if ($to instanceof \DateTime) {
                            $to = $to->format('Y-m-d H:i:s');
                        } elseif (is_object($to)) {
                            $to = '(object)';
                        } elseif (!is_scalar($to)) {
                            $to = var_export($to, true);
                        }
                        $body[] = $field . ': ' . $from . ' -> ' . $to;
                    }
                }
                $body = implode(PHP_EOL, $body);
                $message = Message::create($subject, $body, 1, 2);
                $this->messagePoster->post($message);
            }
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if (!($entity instanceof Message)) {
                $subject = 'Entity ' . get_class($entity) . ' deleted.';
                $changeSet = $unitOfWork->getEntityChangeSet($entity);
                $body = print_r($changeSet, true);
                $message = Message::create($subject, $body, 1, 2);
                $this->messagePoster->post($message);
            }
        }
    }
}
