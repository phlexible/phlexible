<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Before Create Catch Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Phillip Look <pl@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeCatchGetResultPool extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_CATCH_GET_RESULT_POOL;

    /**
     * @var Makeweb_Teasers_Catch
     */
    protected $_catch;

    /**
     * @var mixed
     */
    protected $_select;

    /**
     * @param Makeweb_Teasers_Catch $catch
     * @param mixed        $select
     */
    public function __construct(Makeweb_Teasers_Catch $catch, $select)
    {
        $this->_catch = $catch;
        $this->_select = $select;
    }

    /**
     * @return Makeweb_Teasers_Catch
     */
    public function getCatch()
    {
        return $this->_catch;
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->_select;
    }

}