<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/locks")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * Lock status
     *
     * @return Response
     * @Route("", name="locks_status")
     */
    public function indexAction()
    {
        $lockManager = $this->get('phlexible_lock.lock_manager');

        $out = '<pre>Locks Status' . PHP_EOL . PHP_EOL;
        foreach ($lockManager->findAll() as $lock) {
            $out .= $lock->getId() . PHP_EOL;
        }

        return new Response($out);
    }

}
