<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Apply successor listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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

        $this->connection->update(
            'element',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );

        $this->connection->update(
            'element_history',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );

        // TODO: TreeBundle
        $this->connection->update(
            'tree',
            array(
                'modify_user_id' => $toUid,
            ),
            array(
                'modify_user_id' => $fromUid
            )
        );

        // TODO: TreeBundle
        $this->connection->update(
            'tree_history',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );

        // TODO: TreeBundle
        $this->connection->update(
            'tree_online',
            array(
                'publish_uid' => $toUid,
            ),
            array(
                'publish_uid' => $fromUid
            )
        );

        // TODO: TeaserBundle
        $this->connection->update(
            'teaser',
            array(
                'modify_user_id' => $toUid,
            ),
            array(
                'modify_user_id' => $fromUid
            )
        );

        // TODO: TeaserBundle
        $this->connection->update(
            'teaser_history',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );

        // TODO: TeaserBundle
        $this->connection->update(
            'teaser_online',
            array(
                'publish_user_id' => $toUid,
            ),
            array(
                'publish_user_id' => $fromUid
            )
        );

        $this->connection->update(
            'element_version',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );
    }

}
