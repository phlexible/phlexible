<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FormBundle\FormHandler;

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
     * @param Request $request
     *
     * @return mixed
     */
    public function process(Request $request);
}
