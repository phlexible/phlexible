<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Job;

use Phlexible\Bundle\QueueBundle\Job\ContainerAwareJob;

/**
 * Update file usage Job
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class UpdateUsageJob extends ContainerAwareJob
{
    /**
     * @var integer
     */
    protected $_eid = null;

    /**
     * Set EID
     *
     * @param integer $eid
     */
    public function setEid($eid = null)
    {
        $this->_eid = $eid;
    }

    /**
     * Execute this job
     */
    public function work()
    {
        $output = '';

        $eid = $this->_eid;

        /* @var $db Zend_Db_Adapter_Abstract */
        $dbPool = MWF_registry::get('container')->dbPool;

        $fileUsage = new Makeweb_Elements_Element_FileUsage($dbPool);
        $cnt = $fileUsage->update($eid);

        $output .= 'Files: Deleted '.$cnt['delete'].', inserted '.$cnt['insert'].' rows.';
        $output .= PHP_EOL;

        $folderUsage = new Makeweb_Elements_Element_FolderUsage($dbPool);
        $cnt = $folderUsage->update($eid);

        $output .= 'Folders: Deleted '.$cnt['delete'].', inserted '.$cnt['insert'].' rows.';

        return $output;
    }

}
