<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

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
     * @param ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->db = $connectionManager->default;
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
            $this->db->prefix . 'mediamanager_files',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_files',
            array(
                'modify_user_id' => $toUid,
            ),
            array(
                'modify_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_folders',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_folders',
            array(
                'modify_user_id' => $toUid,
            ),
            array(
                'modify_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_folder_rights',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_folder_rights',
            array(
                'modify_user_id' => $toUid,
            ),
            array(
                'modify_user_id = ?' => $fromUid
            )
        );

        $this->db->update(
            $this->db->prefix . 'mediamanager_site',
            array(
                'create_uid' => $toUid,
            ),
            array(
                'create_uid = ?' => $fromUid
            )
        );
    }
}
