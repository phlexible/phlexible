<?php

class Media_Site_Event_ReplaceFile extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var Media_Site_Abstract
     */
    protected $_site = null;

    /**
     * @var Media_Site_Folder_Abstract
     */
    protected $_folder = null;

    /**
     * @var Media_Site_File_Abstract
     */
    protected $_file = null;

    /**
     * Constructor
     *
     * @param Media_Site_Abstract        $site
     * @param Media_Site_Folder_Abstract $folder
     * @param Media_Site_File_Abstract   $file
     */
    public function __construct(Media_Site_Abstract        $site,
                                Media_Site_Folder_Abstract $folder,
                                Media_Site_File_Abstract   $file)
    {
        $this->_site   = $site;
        $this->_folder = $folder;
        $this->_file   = $file;
    }

    /**
     * Return site
     *
     * @return Media_Site_Abstract
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Return folder
     *
     * @return Media_Site_Folder_Abstract
     */
    public function getFolder()
    {
        return $this->_folder;
    }

    /**
     * Return file
     *
     * @return Media_Site_File_Abstract
     */
    public function getFile()
    {
        return $this->_file;
    }
}