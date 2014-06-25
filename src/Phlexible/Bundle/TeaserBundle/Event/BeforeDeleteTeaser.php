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
 * Before Delete Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_BeforeDeleteTeaser extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::BEFORE_DELETE_TEASER;

    /**
     * @var Makeweb_Teasers_Node
     */
    protected $_node = null;

    /**
     * Constructor
     *
     * @param Makeweb_Teasers_Node $node
     */
    public function __construct(Makeweb_Teasers_Node $node)
    {
        $this->_node = $node;
    }

    /**
     * Return node
     *
     * @return Makeweb_Teasers_Node
     */
    public function getNode()
    {
        return $this->_node;
    }
}
