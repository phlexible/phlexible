<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\View;

use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract view.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractView
{
    /**
     * @var array
     */
    private $parts = [];

    /**
     * @param string $src
     * @param string $type
     *
     * @return $this
     */
    public function addScript($src, $type = 'text/javascript')
    {
        $this->parts[] = '<script type="'.$type.'" src="'.$src.'"></script>'.PHP_EOL;

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
        $this->parts[] = '<script type="'.$type.'">'.PHP_EOL.$script.PHP_EOL.'</script>'.PHP_EOL;

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
        $this->parts[] = '<link href="'.$href.'" media="'.$media.'" rel="'.$rel.'" type="'.$type.'" />';

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
        $this->parts[] = ' <style type="'.$type.'">'.$style.'</style>';

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function get(Request $request)
    {
        $this->collect($request);

        return implode(PHP_EOL, $this->parts);
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    abstract public function collect(Request $request);

    /**
     * @return string
     */
    public function getNoScript()
    {
        return 'Javascript is disabled.<br />Please enable it or update your browser to a recent version.';
    }
}
