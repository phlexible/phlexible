<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Preview controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/form")
 */
class FormController extends Controller
{
    /**
     * @return JsonResponse
     * @Route("/list", name="form_list")
     */
    public function listAction()
    {
        $formHandlers = $this->get('phlexible_form.form_handlers');

        $data = array();
        foreach ($formHandlers->all() as $formHandler) {
            $data[] = array('name' => $formHandler->getName());
        }

        return new JsonResponse($data);
    }
}
