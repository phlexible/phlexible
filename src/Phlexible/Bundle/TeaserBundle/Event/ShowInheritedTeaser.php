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
 * Show Inherited Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_ShowInheritedTeaser extends Makeweb_Teasers_Event_BeforeShowInheritedTeaser
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::SHOW_INHERITED_TEASER;

    /**
     * @var int
     */
    protected $_hideInheritId = null;

    /**
     * Constructor
     *
     * @param int $treeId
     * @param int $eid
     * @param int $teaserEid
     * @param int $layoutarreaId
     * @param int $hideInheritId
     */
    public function __construct($treeId, $eid, $teaserEid, $layoutarreaId, $hideInheritId)
    {
        parent::__construct($treeId, $eid, $teaserEid, $layoutarreaId);

        $this->_hideInheritId = $hideInheritId;
    }

    /**
     * Return tree ID
     *
     * @return int
     */
    public function getHideInheritId()
    {
        return $this->_hideInheritId;
    }
}
