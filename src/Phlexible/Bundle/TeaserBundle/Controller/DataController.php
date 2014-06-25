<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataController extends Controller
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $query = $this->_getParam('query');

        $results = array_merge(Makeweb_Elements_Search_Eid::search($query),
                               Makeweb_Elements_Search_Title::search($query));

        $data = array();
        foreach($results as $result)
        {
            /* @var $result MWF_Core_Search_Result */
            $row = $result->toArray();

            $data[] = array(
                'id'    => $row['id'],
                'title' => $row['title']
            );
        }

        $this->getResponse()->setAjaxPayload(array('results' => $data));
    }

}
