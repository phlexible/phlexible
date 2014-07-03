<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 2943 2007-04-18 09:00:40Z swentz $
 */

/**
 * Makeweb System Elements Notification
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Matthias Harmuth <mharmuth@brainbits.net>
 * @copyright   2011 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Notifications
{
    /**
     * @var MWF_Db_Pool
     */
    protected $_dbPool = null;

    /**
     * table name
     *
     * @var string
     */
    protected $_name = 'element_notifications';

    /**
     * Constructor
     *
     * @param MWF_Db_Pool $dbPool
     */
    public function __construct(MWF_Db_Pool $dbPool)
    {
        $this->_dbPool = $dbPool;
    }

    /**
     * save new notification
     *
     * @param int    $tid
     * @param string $language
     *
     * @return int
     */
    public function save($tid, $language)
    {
        $db = $this->_dbPool->write;

        // insert data
        $data = array(
            'tid'         => $tid,
            'language'    => $language,
            'create_time' => new Zend_Db_Expr('NOW()'),
            'update_time' => new Zend_Db_Expr('NOW()'),
        );

        $result = $db->insert($db->prefix . $this->_name, $data);

        return $result;
    }

    /**
     * update notification
     *
     * @param int    $id
     * @param string $language
     *
     * @return int
     */
    public function update($id, $language)
    {
        $db = $this->_dbPool->write;

        $data = array(
            'language'    => $language,
            'update_time' => new Zend_Db_Expr('NOW()'),
        );

        $result = $db->update($db->prefix . $this->_name, $data, 'id = ' . $id);

        return $result;
    }

    /**
     * delete notification
     *
     * @param int $id
     *
     * @return int
     */
    public function delete($id)
    {
        $db = $this->_dbPool->write;

        return $db->delete($db->prefix . $this->_name, 'id = ' . $id);
    }

    /**
     * get notification by tid
     *
     * @param int $tid
     *
     * @return array
     */
    public function getNotificationByTid($tid, $language)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from($db->prefix . $this->_name)
            ->where('tid = ?', $tid)
            ->where('language = ?', $language)
            ->limit(1);

        $result = $db->fetchAll($select);

        return $result;
    }
}