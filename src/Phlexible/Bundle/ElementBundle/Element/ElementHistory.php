<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element;

use Phlexible\Component\Database\ConnectionManager;

/**
 * Element history
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementHistory
{
    const ACTION_CREATE = 'create';
    const ACTION_CREATE_VERSION = 'createVersion';
    const ACTION_SAVE = 'save';
    const ACTION_SAVE_MASTER = 'saveMaster';
    const ACTION_SAVE_LANGUAGE = 'saveLanguage';
    const ACTION_LOCK = 'lock';
    const ACTION_UNLOCK = 'unlock';

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
     * Insert new history entry
     *
     * @param string $action
     * @param string $eid
     * @param string $uid
     * @param string $version
     * @param string $language
     * @param string $comment
     *
     * @return $this
     */
    public function insert($action, $eid, $uid, $version = null, $language = null, $comment = null)
    {
        $this->db->insert(
            $this->db->prefix . 'element_history',
            array(
                'eid'         => $eid,
                'language'    => $language,
                'version'     => $version,
                'action'      => $action,
                'comment'     => $comment,
                'create_time' => $this->db->fn->now(),
                'create_uid'  => $uid,
            )
        );

        return $this;
    }

}
