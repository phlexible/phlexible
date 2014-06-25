<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Plugin;

use Dwoo\Block\Plugin;
use Phlexible\Bundle\FrontendAssetBundle\Collector\Block;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Scripts plugin
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Scripts extends Plugin
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $output;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param array  $rest
     *
     * @return string
     */
    public function begin(array $rest = array())
    {
        if (isset($rest['name'])) {
            $this->name = $rest['name'];
            unset($rest['name']);
        }
        if (isset($rest['output'])) {
            $this->output = $rest['output'];
            unset($rest['output']);
        }
        $this->files = $rest;
    }

    /**
     * @return string
     */
    public function process()
    {
        $debug = $this->getContainer()->getParameter('kernel.debug');

        if ($this->name) {
            $collector = $this->getContainer()->get('phlexible_frontend_asset.collector');
            $blocks = $collector->collect();
            $block = $blocks->getBlock($this->name);
        } else {
            $block = new Block(uniqid());
        }

        foreach ($this->files as $file) {
            $block->append($file);
        }

        if (!count($block->getFiles())) {
            return '';
        }

        if ($debug) {
            $scripts = array();

            foreach ($block->getFiles() as $uri) {
                $scripts[] = trim(str_replace('$url', $uri, $this->buffer));
            }

            return trim(implode(PHP_EOL, $scripts));
        } else {
            $dumper = $this->getContainer()->get('phlexible_frontend_asset.dumper.script');
            if ($this->output) {
                $outUri = $this->output;
            } else {
                $outUri = sprintf('scripts-%s.js', $block->getName());
            }
            $outFilename = $dumper->dump($block, $outUri);
            $time = filemtime($outFilename);
            $outUri .= '?' . $time;
            $outUri = '/' . rtrim($outUri, '/');
            $script = trim(str_replace('$url', $outUri, $this->buffer));

            return $script;
        }
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->core->getData()['container'];
    }
}
