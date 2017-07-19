<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaToolBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Media tools configuration.
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
                ->arrayNode('exiftool')
                    ->canBeUnset()
                ->end()
                ->arrayNode('poppler')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('pdfinfo')->defaultValue('pdfinfo')->end()
                        ->scalarNode('pdftotext')->defaultValue('pdftotext')->end()
                        ->scalarNode('pdftohtml')->defaultValue('pdftohtml')->end()
                        ->scalarNode('timeout')->defaultValue(60)->end()
                    ->end()
                ->end()
                ->arrayNode('ffmpeg')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('ffprobe')->defaultValue('ffprobe')->end()
                        ->scalarNode('ffmpeg')->defaultValue('ffmpeg')->end()
                    ->end()
                ->end()
                ->arrayNode('imagine')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.imagine.imagick')->end()
                    ->end()
                ->end()
                ->arrayNode('image_analyzer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('phlexible_media_tool.image_analyzer.driver.imagick')->end()
                    ->end()
                ->end()
                ->arrayNode('mime')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('file')->defaultValue('file')->end()
                        ->scalarNode('magicfile')->defaultNull()->end()
                        ->scalarNode('use_extension_fallback')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('mime_detector')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')->defaultValue('phlexible_media_tool.mime.adapter.file')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
