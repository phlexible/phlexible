<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaToolBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media tools configuration
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phlexible_media_tool');

        $rootNode
            ->children()
                ->arrayNode('swftools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pdf2swf')->defaultValue('pdf2swf')->end()
                        ->scalarNode('swfdump')->defaultValue('swfdump')->end()
                        ->scalarNode('swfcombine')->defaultValue('swfcombine')->end()
                    ->end()
                ->end()
                ->arrayNode('pdftotext')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pdftotext')->defaultValue('pdftotext')->end()
                        ->scalarNode('pdfinfo')->defaultValue('pdfinfo')->end()
                    ->end()
                ->end()
                ->arrayNode('mime')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('file')->defaultValue('file')->end()
                        ->scalarNode('magicfile')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('imagemagick')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('identify')->defaultValue('identify')->end()
                        ->scalarNode('convert')->defaultValue('convert')->end()
                        ->scalarNode('mogrify')->defaultValue('mogrify')->end()
                    ->end()
                ->end()
                ->arrayNode('ffmpeg')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ffprobe')->defaultValue('ffprobe')->end()
                        ->scalarNode('ffmpeg')->defaultValue('ffmpeg')->end()
                    ->end()
                ->end()
                ->arrayNode('image_analyzer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.image_analyzer.driver.imagemagick')->end()
                    ->end()
                ->end()
                ->arrayNode('image_converter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.image_converter.driver.imagemagick')->end()
                    ->end()
                ->end()
                ->arrayNode('mime_detector')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')->defaultValue('phlexible_media_tool.mime.adapter.fallback')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}