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
            if (!class_exists("\PHPExiftool\Reader")) {
                throw new \LogicException('To configure exiftool, you must first install the alchemy/php-exiftool package.');
            }
            $loader->load('exiftool.yml');
        }

        if (isset($config['poppler'])) {
            if (!class_exists("\Poppler\Driver\Pdfinfo")) {
                throw new \LogicException('To configure poppler, you must first install the php-poppler/php-poppler package.');
            }
            $container->setParameter('phlexible_media_tool.poppler.configuration', array(
                'pdfinfo.binaries' => $config['poppler']['pdfinfo'],
                'pdftotext.binaries' => $config['poppler']['pdftotext'],
                'pdftohtml.binaries' => $config['poppler']['pdftohtml'],
                'timeout' => $config['poppler']['timeout'],
            ));
            $loader->load('poppler.yml');
        }

        if (isset($config['ffmpeg'])) {
            if (!class_exists("\FFMpeg\FFMpeg")) {
                throw new \LogicException('To configure ffmpeg, you must first install the php-ffmpeg/php-ffmpeg package.');
            }
            $container->setParameter('phlexible_media_tool.ffmpeg.configuration', array(
                'ffprobe.binaries' => $config['ffmpeg']['ffprobe'],
                'ffmpeg.binaries' => $config['ffmpeg']['ffmpeg'],
            ));
            $loader->load('ffmpeg.yml');
        }

        $container->setAlias('phlexible_media_tool.image_analyzer.driver', $config['image_analyzer']['driver']);
        $container->setAlias('phlexible_media_tool.imagine', $config['imagine']['driver']);
    }
}
