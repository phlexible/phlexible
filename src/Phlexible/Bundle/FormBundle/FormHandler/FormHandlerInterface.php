<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Form handler interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FormHandlerInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return Form
     */
    public function createForm();

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handleRequest(Request $request);
}
