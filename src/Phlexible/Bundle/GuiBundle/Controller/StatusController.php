<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route as RoutingRoute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/gui")
 * @Security("is_granted('debug')")
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
        $output .= '<a href="'.$this->generateUrl('gui_status_components').'">components</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_callbacks').'">callbacks</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_listeners').'">listeners</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_routes').'">routes</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_php').'">php</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_versions').'">versions</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_load').'">load</a><br/>';

        return new Response($output);
    }

    /**
     * List components
     *
     * @return Response
     * @Route("/components", name="gui_status_components")
     */
    public function componentsAction()
    {
        $components = $this->container->getParameter('kernel.bundles');

        $output = '<pre>Components:'.PHP_EOL.PHP_EOL;

        foreach ($components as $id => $class) {
            $output .= str_pad($id.':', 25).str_pad($class, 65).PHP_EOL;
        }

        return new Response($output);
    }

    /**
     * Show callbacks
     *
     * @return Response
     * @Route("/callbacks", name="gui_status_callbacks")
     */
    public function callbacksAction()
    {
        $components = $this->container->getParameter('kernel.bundles');

        $allCallbacks = array();
        $out = '';

        foreach ($components as $id => $class) {
            $cur = str_repeat('-', 3).'<a name="'.$id.'">'.str_pad(' '.$id.' ', 20, '-').'</a>'.str_repeat('-', 70).PHP_EOL.PHP_EOL;

            $callbacks = get_class_methods($class);
            $reflection = new \ReflectionClass('Symfony\Component\HttpKernel\Bundle\Bundle');
            $methods = $reflection->getMethods();
            $nonCallbacks = array('__construct', 'initContainer', 'setCallbacks', 'setDependencies', 'setDescription', 'setName', 'setOrder', 'setPath', 'getFile', 'setFile', 'getPath', 'getContainer', 'setContainer', 'getControllerDirectory', 'setControllerDirectory', 'init');
            foreach ($methods as $method) {
                $nonCallbacks[] = $method->getName();
            }
            $callbacks = array_diff($callbacks, $nonCallbacks);

            if (!count($callbacks)) {
                continue;
            }

            foreach ($callbacks as $callback) {
                if (!isset($allCallbacks[$callback])) {
                    $allCallbacks[$callback] = 0;
                }
                $allCallbacks[$callback]++;

                $url = $this->generateUrl('gui_status_callback', array('callback' => $callback, 'component' => $id));
                $cur .= "<a href='$url'>$id::$callback()</a>" . PHP_EOL;
            }

            $out .= $cur . PHP_EOL;
        }

        ksort($allCallbacks);

        $output = "<pre>" . str_repeat('=', 3).' Callbacks '.str_repeat('=', 80).PHP_EOL.PHP_EOL;
        foreach ($allCallbacks as $callback => $count) {
            $url = $this->generateUrl('gui_status_callback', array('callback' => $callback));
            $output .= "<a href='$url'>$callback()</a> ($count)" . PHP_EOL;
        }

        $output .= PHP_EOL . PHP_EOL . $out;

        return new Response($output);
    }

    /**
     * Show callback
     *
     * @param string $callback
     * @param string $component
     *
     * @return Response
     * @Route("/callback", name="gui_status_callback")
     */
    public function callbackAction($callback, $component = null)
    {
        $components = $this->container->getParameter('kernel.bundles');

        $output = '<pre>';

        $result = null;
        if (!$component) {
            $output .= 'Callback: '.$callback.'()'.PHP_EOL.PHP_EOL;
            $result = array();
            foreach ($components as $class) {
                if (method_exists($class, $callback)) {
                    $bundle = new $class();
                    $result = array_merge($result, $bundle->$callback());
                }
            }
        } else {
            $class = $components[$component];
            if (method_exists($class, $callback)) {
                $bundle = new $class();
                $result = $bundle->$callback();
                $output .= 'Callback:  '.$callback.'()'.PHP_EOL;
                $output .= 'Component: '.$component.PHP_EOL.PHP_EOL;
            }
        }

        if ($result) {
            $output .= print_r($result, true);
        }

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
            //sort($observers);

            if (!$listenerName) {
                $listenerName = '(global)';
            }

            $output .= PHP_EOL . PHP_EOL;
            $output .= str_repeat('-', 3);
            $output .= '<a name="' . $listenerName . '"></a>' . str_pad(' ' . $listenerName . ' ', 80, '-');
            $output .= PHP_EOL . PHP_EOL;

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
     * Show routes
     *
     * @return Response
     * @Route("/routes", name="gui_status_routes")
     */
    public function routesAction()
    {
        $router = $this->get('router');
        $nameParser = $this->get('controller_name_converter');

        $routes = $router->getRouteCollection();
        $paths = array();
        foreach ($routes as $name => $route) {
            /* @var $route RoutingRoute  */

            /*
            $data = array();
            $vars = $route->getVariables();
            foreach ($vars as $var) {
                $data[$var] = '{' . $var . '}';
            }
            */

            if ($route->hasDefault('_controller')) {
                try {
                    $route->setDefault('_controller', $nameParser->build($route->getDefault('_controller')));
                } catch (\InvalidArgumentException $e) {
                }
            }

            $paths[$name] = $route->getPath();
        }

        ksort($paths);

        $output = '<pre>';

        foreach ($paths as $name => $path) {
            $output .= str_pad($name, 50) . ' ' . $path . PHP_EOL;
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
     * Show versions
     *
     * @return Response
     * @Route("/versions", name="gui_status_versions")
     */
    public function versionsAction()
    {
        $output = '';
        $output .= '<div>PHP: ' . PHP_VERSION . '</div>';
        $output .= '<div>Zend Framework: ' . \Zend_Version::VERSION . '</div>';
        $output .= '<div>Doctrine: ' . \Doctrine_Core::VERSION . '</div>';

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

