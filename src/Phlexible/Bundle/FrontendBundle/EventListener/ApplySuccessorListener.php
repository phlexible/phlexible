<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\EventListener;

use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Apply successor listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    public function onApplySuccessor(ApplySuccessorEvent $event, array $params = array())
    {
        $db = $params['container']->dbPool->write;

        $fromUser = $event->getFromUser();
        $toUser   = $event->getToUser();

        $fromUid = $fromUser->getId();
        $toUid   = $toUser->getId();

        $db->update(
            $db->prefix . 'request_urls',
            array(
                'create_uid'  => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );
    }
}