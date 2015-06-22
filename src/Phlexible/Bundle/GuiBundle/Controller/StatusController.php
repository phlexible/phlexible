<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/gui")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * List status actions
     *
     * @return Response
     * @Route("", name="gui_status")
     */
    public function indexAction()
    {
        $output = '';
        $output .= '<a href="'.$this->generateUrl('gui_status_listeners').'">listeners</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_php').'">php</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_load').'">load</a><br/>';

        return new Response($output);
    }

    /**
     * Show events
     *
     * @return Response
     * @Route("/listeners", name="gui_status_listeners")
     */
    public function listenersAction()
    {
        $dispatcher = $this->get('event_dispatcher');

        $listenerNames = array_keys($dispatcher->getListeners());
        sort($listenerNames);

        $output = '<pre>';
        $output .= str_repeat('=', 3) . str_pad(' Events / Listeners ', 80, '=') . PHP_EOL . PHP_EOL;

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);

            $output .= $listenerName . ' (<a href="#' . $listenerName . '">' . count($listeners) . ' listeners</a>)' . PHP_EOL;
        }

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);

            if (!$listenerName) {
                $listenerName = '(global)';
            }

            $output .= PHP_EOL . PHP_EOL . str_repeat('-', 3) . '<a name="' . $listenerName . '"></a>' . str_pad(' ' . $listenerName . ' ', 80, '-') . PHP_EOL . PHP_EOL;

            foreach ($listeners as $listener) {
                if (is_array($listener)) {
                    if (is_object($listener[0])) {
                        $listener = get_class($listener[0]) . '->' . $listener[1] . '()';
                    } else {
                        $listener = implode('::', $listener) . '()';
                    }
                }
                $output .= '* ' . $listener . PHP_EOL;
            }
        }

        return new Response($output);
    }

    /**
     * phpinfo
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/php", name="gui_status_php")
     */
    public function phpAction(Request $request)
    {
        $show = $request->query->get('show', -1);

        ob_start();
        phpinfo($show);
        $output = ob_get_clean();

        return new Response($output);
    }

    /**
     * Show load
     *
     * @return Response
     * @Route("/load", name="gui_status_load")
     */
    public function loadAction()
    {
        $output = print_r(sys_getloadavg(), true);

        return new Response($output);
    }
}

