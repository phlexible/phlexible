<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category  MAKEweb
 * @package   Makeweb_Frontendmediamanager
 * @copyright 2011 brainbits GmbH (http://www.brainbits.net)
 * @version   SVN: $Id: PaginatorAdapter.php 1497 2011-06-03 12:59:02Z swentz $
 */

/**
 * Frontendmediamanager Folder Paginator Adapter
 *
 * @category  MAKEweb
 * @package   Makeweb_Frontendmediamanager
 * @author    Michael van Engelshoven <mve@brainbits.net>
 * @copyright 2011 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Frontendmediamanager_Folder_PaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
    /**
     * Instance of Media Site Folder
     *
     * @var Media_SiteDb_Folder
     */
    protected $_folder;

    /**
     * Constructs a new paginator adapter
     *
     * @param Media_SiteDb_Folder $folder Media Site Folder
     */
    public function __construct(Media_SiteDb_Folder $folder)
    {
        $this->_folder = $folder;
    }

    /**
     * Returns the total count
     *
     * @return int
     */
    public function count()
    {
        return $this->_folder->getNumFiles();
    }

    /**
     * Returns paged files
     *
     * @param  integer $offset
     * @param  integer $itemCountPerPage
     *
     * @return array Array with files
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->_folder->getFiles($offset, $itemCountPerPage);
    }
}
