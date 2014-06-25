<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Elementtypes listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param ConnectionManager $dbPool
     */
    public function __construct(ConnectionManager $dbPool)
    {
        $this->db = $dbPool->default;
    }

    /**
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $fromUser = $event->getFromUser();
        $toUser   = $event->getToUser();

        $fromUid = $fromUser->getId();
        $toUid   = $toUser->getId();

        $this->db->update(
            $this->db->prefix . 'elementtype',
            array(
                'create_uid'  => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'elementtype',
            array(
                'modify_uid'  => $toUid,
            ),
            array(
                'modify_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'elementtype_version',
            array(
                'modify_uid'  => $toUid,
            ),
            array(
                'modify_uid = ?' => $fromUid
            )
        );
    }
}