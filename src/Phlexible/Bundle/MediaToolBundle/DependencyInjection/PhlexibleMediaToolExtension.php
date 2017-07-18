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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media tool extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaToolExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('imageanalyzer.yml');
        $loader->load('imagine.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        if (isset($config['exiftool'])) {
            $loader->load('exiftool.yml');
        }

        if (isset($config['swftools'])) {
            $container->setParameter('phlexible_media_tool.swftools.configuration', array(
                'pdf2swf.binaries'    => $config['swftools']['pdf2swf'],
                'swfrender.binaries'  => $config['swftools']['swfrender'],
                'swfextract.binaries' => $config['swftools']['swfextract'],
                'timeout'             => $config['swftools']['timeout']
            ));
            $loader->load('swftools.yml');
        }

        if (isset($config['poppler'])) {
            $container->setParameter('phlexible_media_tool.poppler.configuration', array(
                'pdfinfo.binaries' => $config['poppler']['pdfinfo'],
                'pdftotext.binaries' => $config['poppler']['pdftotext'],
                'pdftohtml.binaries' => $config['poppler']['pdftohtml'],
                'timeout' => $config['poppler']['timeout'],
            ));
            $loader->load('poppler.yml');
        }

        if (isset($config['ffmpeg'])) {
            $container->setParameter('phlexible_media_tool.ffmpeg.configuration', array(
                'ffprobe.binaries' => $config['ffmpeg']['ffprobe'],
                'ffmpeg.binaries' => $config['ffmpeg']['ffmpeg'],
            ));
            $loader->load('ffmpeg.yml');
        }

        $loader->load('mime.yml');
        if ($config['mime']['use_extension_fallback']) {
            $container->setParameter('phlexible_media_tool.mime.file', $config['mime']['file']);
            $container->setParameter('phlexible_media_tool.mime.magicfile', $config['mime']['magicfile']);

            $container->findDefinition('phlexible_media_tool.mime.adapter.fallback')->replaceArgument(1, $config['mime_detector']['adapter']);

            $container->setAlias('phlexible_media_tool.mime.adapter', 'phlexible_media_tool.mime.adapter.fallback');
        } else {
            $container->setAlias('phlexible_media_tool.mime.adapter', $config['mime_detector']['adapter']);

        }

        $container->setAlias('phlexible_media_tool.image_analyzer.driver', $config['image_analyzer']['driver']);
        $container->setAlias('phlexible_media_tool.imagine', $config['imagine']['driver']);
    }
}
