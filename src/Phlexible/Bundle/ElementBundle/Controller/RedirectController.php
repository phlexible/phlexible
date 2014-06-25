<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Redirect controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/redirect")
 * @Security("is_granted('elements')")
 */
class RedirectController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="elements_redirect_list")
     */
    public function listAction(Request $request)
    {
        $tid            = $request->get('tid');
        $language       = $request->get('language');

        $db = $this->getContainer()->dbPool->default;

        try {
            $select = $db->select(array('url'))
                         ->from($db->prefix.'element_redirect', 'url')
                         ->where("tid = ?",      $tid)
                         ->where("language = ?", $language);

            $data = $db->fetchAll($select);
        } catch (Zend_Db_Exception $e) {
            $data = array();
        }

        return new JsonResponse(array('redirects' => $data, 'count' => count($data)));
    }
}
