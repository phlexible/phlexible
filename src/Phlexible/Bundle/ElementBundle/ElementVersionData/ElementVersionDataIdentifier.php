<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    Makeweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

use Phlexible\Bundle\LockBundle\LockIdentifier;

/**
 * Element Version Identifier
 *
 * @category    Makeweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @see         Brainbits_Identifier
 */
class Makeweb_Elements_Element_Version_Data_Identifier extends LockIdentifier
{
    /**
     * Constructor
     * Create a new Element Version Data Identifier
     *
     * @param int    $eid
     * @param string $language
     * @param int    $version
     * @param string $mode
     *
     * @throws Brainbits_Identifier_Exception
     */
    public function __construct($eid, $language, $version, $mode)
    {
        parent::__construct($eid, $language, $version, $mode);
    }
}
