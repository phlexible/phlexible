<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;
use Phlexible\Component\Database\ConnectionManager;

/**
 * Apply successor listener
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
        $toUser = $event->getToUser();

        $fromUid = $fromUser->getId();
        $toUid = $toUser->getId();

        $this->db->update(
            $this->db->prefix . 'element',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_history',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree',
            array(
                'modify_uid' => $toUid,
            ),
            array(
                'modify_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree_history',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree_online',
            array(
                'publish_uid' => $toUid,
            ),
            array(
                'publish_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree_teasers',
            array(
                'modify_uid' => $toUid,
            ),
            array(
                'modify_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree_teasers_history',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_tree_teasers_online',
            array(
                'publish_uid' => $toUid,
            ),
            array(
                'publish_uid = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'element_version',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );
    }

}
