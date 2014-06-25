<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Options controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/options")
 * @Security("is_granted('media')")
 */
class OptionsController extends Controller
{
    /**
     * Save mediamanager options
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/savemedia", name="mediamanager_options_savemedia")
     */
    public function savemediaAction(Request $request)
    {
        $disableFlash = (bool) $request->get('disable_flash', false);
        $enableUploadSort = (bool) $request->get('enable_upload_sort', false);

        $user = $this->getUser();
        $user->setProperty('mediamanager.upload.disable_flash', $disableFlash);
        $user->setProperty('mediamanager.upload.enable_upload_sort', $enableUploadSort);

        return new ResultResponse(true, 'Media settings saved.');
    }
}
