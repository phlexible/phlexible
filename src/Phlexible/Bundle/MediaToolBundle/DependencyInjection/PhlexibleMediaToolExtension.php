<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaToolBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Media tool extension
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
        $loader->load('mime.yml');
        $loader->load('ffmpeg.yml');
        $loader->load('imagemagick.yml');
        $loader->load('swftools.yml');
        $loader->load('pdftotext.yml');
        $loader->load('imageanalyzer.yml');
        $loader->load('imageconverter.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_tool.swftools.pdf2swf', $config['swftools']['pdf2swf']);
        $container->setParameter('phlexible_media_tool.swftools.swfdump', $config['swftools']['swfdump']);
        $container->setParameter('phlexible_media_tool.swftools.swfcombine', $config['swftools']['swfcombine']);
        $container->setParameter('phlexible_media_tool.pdftotext.pdftotext', $config['pdftotext']['pdftotext']);
        $container->setParameter('phlexible_media_tool.pdftotext.pdfinfo', $config['pdftotext']['pdfinfo']);
        $container->setParameter('phlexible_media_tool.imagemagick.identify', $config['imagemagick']['identify']);
        $container->setParameter('phlexible_media_tool.imagemagick.convert', $config['imagemagick']['convert']);
        $container->setParameter('phlexible_media_tool.imagemagick.mogrify', $config['imagemagick']['mogrify']);
        $container->setParameter('phlexible_media_tool.ffmpeg.ffprobe', $config['ffmpeg']['ffprobe']);
        $container->setParameter('phlexible_media_tool.ffmpeg.ffmpeg', $config['ffmpeg']['ffmpeg']);
        $container->setParameter('phlexible_media_tool.mime.file', $config['mime']['file']);
        $container->setParameter('phlexible_media_tool.mime.magicfile', $config['mime']['magicfile']);

        $container->setAlias('phlexible_media_tool.image_analyzer.driver', $config['image_analyzer']['driver']);
        $container->setAlias('phlexible_media_tool.image_converter.driver', $config['image_converter']['driver']);
        $container->setAlias('phlexible_media_tool.mime.adapter', $config['mime_detector']['adapter']);
    }
}
