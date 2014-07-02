<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Exception;

use Phlexible\Bundle\QueueBundle\Job\ContainerAwareJob;

/**
 * Update online catchteaser helper job
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class UpdateHelperOnlineJob extends ContainerAwareJob
{
    /**
     * @var int
     */
    protected $_eid = null;

    /**
     * Set EID
     *
     * @param int $eid
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

        /* @var $helper Makeweb_Teasers_Catch_Helper */
        $helper = MWF_registry::get('container')->get('phlexible_teaser.catch.helper');

        $cnt = $helper->updateOnline($eid);

        $output .= 'Wrote '.$cnt.' helper entries.';

        return $output;
    }

}
