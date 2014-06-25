<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Plugin;

/**
 * Dwoo documentlist plugin
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class Documentlist extends \Dwoo\Plugin
{
    /**
     * ID of media folder
     * @var integer
     */
    protected $_folderId;

    /**
     * Path to partial for rendering list
     * @var string
     */
    protected $_partial;

    /**
     * Executes the helper
     *
     * @param integer $folderId     ID of media folder
     * @param string  $partial      Path to partial
     * @param string  $docView      chosen documentlist_view from elementtype
     * @param array   $data         list with documents
     * @param integer $countPerPage documentcount per page
     * @param boolean $filter       file filter active
     */
    public function process($folderId, $partial, $docView, $data, $countPerPage = 10, $filter = false)
    {
        $request = $this->core->getData()['request'];
        $tid      = $request->getTid();
        $language = $request->getLanguage();

        // variables for partial view
        $partialAssigns = array(
            'documents'    => $data,
            'tid'          => $tid,
            'language'     => $language,
            'doc_view'     => $docView,
            'folder'       => $folderId,
            'countPerPage' => $countPerPage,
            'filter'	   => $filter,
        );

        if (!function_exists('Dwoo_Plugin_include'))
        {
            $this->core->getLoader()->loadPlugin('include');
        }

        return Dwoo_Plugin_include($this->core, $partial, null, null, null, $partialAssigns);
    }
}