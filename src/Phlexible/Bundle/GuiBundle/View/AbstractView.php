<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\View;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Abstract view
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractView
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $parts = array();

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $src
     * @param string $type
     *
     * @return $this
     */
    public function addScript($src, $type = 'text/javascript')
    {
        $this->parts[] = '<script type="' . $type . '" src="' . $src . '"></script>' . PHP_EOL;

        return $this;
    }

    /**
     * @param string $script
     * @param string $type
     *
     * @return $this
     */
    public function addInlineScript($script, $type = 'text/javascript')
    {
        $this->parts[] = '<script type="' . $type . '">' . PHP_EOL . $script . PHP_EOL . '</script>' . PHP_EOL;

        return $this;
    }

    /**
     * @param string $href
     * @param string $type
     * @param string $rel
     * @param string $media
     *
     * @return $this
     */
    public function addLink($href, $type = 'text/css', $rel = 'stylesheet', $media = 'screen')
    {
        $this->parts[] = '<link href="' . $href . '" media="' . $media . '" rel="' . $rel . '" type="' . $type . '" />';

        return $this;
    }

    /**
     * @param string $style
     * @param string $type
     *
     * @return $this
     */
    public function addInlineStyle($style, $type = 'text/css')
    {
        $this->parts[] = ' <style type="' . $type . '">' . $style . '</style>';

        return $this;
    }

    /**
     * @param Request                  $request
     * @param SecurityContextInterface $securityContext
     *
     * @return string
     */
    public function get(Request $request, SecurityContextInterface $securityContext)
    {
        $this->collect($request, $securityContext);

        return implode(PHP_EOL, $this->parts);
    }

    /**
     * @param Request                  $request
     * @param SecurityContextInterface $securityContext
     *
     * @return $this
     */
    abstract public function collect(Request $request, SecurityContextInterface $securityContext);

    /**
     * @return string
     */
    public function getNoScript()
    {
        return 'Javascript is disabled.<br />Please enable it or update your browser to a recent version.';
    }
}