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
 * Before Show Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeShowTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_SHOW_TEASER;

    /**
     * @var Makeweb_Teasers_Node
     */
    protected $_node = null;

    /**
     * Constructor
     *
     * @param integer $tid
     * @param integer $teaserEid
     * @param ineger  $teaserId
     * @param string  $type
     */
    public function __construct(Makeweb_Teasers_Node $node)
    {
        $this->_node = $node;
    }

    /**
     * Return teaser node
     *
     * @return Makeweb_Teasers_Node
     */
    public function getNode()
    {
        return $this->_node;
    }
}
