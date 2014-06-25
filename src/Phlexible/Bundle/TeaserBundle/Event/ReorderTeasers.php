<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Reorder Teasers Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Peter Fahssel <pfahsel@brainbits.net>
 * @copyright   2012 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_ReorderTeasers extends Makeweb_Teasers_Event_BeforeReorderTeasers
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::REORDER_TEASERS;
}