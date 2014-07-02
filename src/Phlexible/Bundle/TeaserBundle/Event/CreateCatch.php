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
 * Create Catch Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_CreateCatch extends Makeweb_Teasers_Event_BeforeCreateCatch
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::CREATE_CATCH;

    /**
     * @var int
     */
    protected $_teaserId = null;

    /**
     * Constructor
     *
     * @param int $treeId
     * @param int $eid
     * @param int $layoutAreaId
     * @param int $teaserId
     */
    public function __construct($treeId, $eid, $layoutAreaId, $teaserId)
    {
        $this->_teaserId = $teaserId;
        parent::__construct($treeId, $eid, $layoutAreaId);
    }

    /**
     * get teaser id
     *
     * @return int
     */
    public function getTeaserId()
    {

        return $this->_teaserId;
    }
}
