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
     * @param string $baseUrl
     * @param string $appTitle
     * @param string $projectTitle
     *
     * @return string
     */
    public function getNoScript($baseUrl, $appTitle, $projectTitle)
    {
        $title   = $appTitle . ' - ' . $projectTitle;
        $text    = 'Javascript is disabled.<br />Please enable it or update your browser to a recent version.';

        return $this->getHint($title, $text, $baseUrl);
    }

    /**
     * @param string $title
     * @param string $text
     * @param string $baseUrl
     *
     * @return string
     */
    public function getHint($title, $text, $baseUrl)
    {
        return <<<EOL
<div id="ext-comp-1002" class="x-window m-auth-logout-window x-window-noborder" style="position: absolute; width: 420px; height: 282px; margin: -141px 0 0 -210px; display: block; left: 50%; top: 50%; visibility: visible; z-index: 9003; ">
  <div class="x-window-tl">
    <div class="x-window-tr">
      <div class="x-window-tc">
        <div class="x-window-header x-window-header-noborder x-unselectable" id="ext-gen16" style="-webkit-user-select: none; ">
          <span class="x-window-header-text" id="ext-gen24">$title</span>
        </div>
      </div>
    </div>
  </div>
  <div class="x-window-bwrap" id="ext-gen17">
    <div class="x-window-ml">
      <div class="x-window-mr">
        <div class="x-window-mc">
          <div class="x-window-body x-window-body-noborder x-border-layout-ct" id="ext-gen18" style="width: 408px; height: 252px; ">
            <div id="ext-comp-1003" class="x-panel x-border-panel" style="text-align: center; width: 408px; left: 0px; top: 0px; ">
              <div class="x-panel-bwrap" id="ext-gen26">
                <div class="x-panel-body x-panel-body-noheader" id="ext-gen27" style="width: 406px; height: 148px; ">
                  <img src="$baseUrl/resources/app/logo" width="300" height="120" style="padding-top: 15px">
                </div>
              </div>
            </div>
            <div id="ext-comp-1004" class="x-panel x-border-panel" style="width: 408px; left: 0px; top: 150px; ">
              <div class="x-panel-tl">
                <div class="x-panel-tr">
                  <div class="x-panel-tc"></div>
                </div>
              </div>
              <div class="x-panel-bwrap" id="ext-gen28">
                <div class="x-panel-ml">
                  <div class="x-panel-mr">
                    <div class="x-panel-mc">
                      <div class="x-panel-body" id="ext-gen29" style="padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px; width: 376px; height: 69px; ">
                        <div id="ext-comp-1005" class="x-panel x-panel-noborder">
                          <div class="x-panel-bwrap" id="ext-gen30">
                            <div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" id="ext-gen31" style="padding-top: 2px; padding-bottom: 20px; text-align: center; ">$text</div>
                          </div>
                        </div>
                        <div id="ext-comp-1006" class="x-panel x-panel-noborder">
                          <div class="x-panel-bwrap" id="ext-gen32">
                            <div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" id="ext-gen33" style="text-align: center; ">
                              <a href="$baseUrl">Reload</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="x-panel-bl x-panel-nofooter">
                  <div class="x-panel-br">
                    <div class="x-panel-bc"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="x-window-bl x-panel-nofooter">
      <div class="x-window-br">
        <div class="x-window-bc"></div>
      </div>
    </div>
  </div>
  <a href="#" class="x-dlg-focus" tabindex="-1" id="ext-gen21">&nbsp;</a>
</div>
EOL;
    }
}