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
        $loader->load('swftools.yml');
        $loader->load('poppler.yml');
        $loader->load('imageanalyzer.yml');
        $loader->load('imagine.yml');

        $configuration = $this->getConfiguration($config, $container);
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('phlexible_media_tool.swftools.configuration', array(
            'pdf2swf.binaries'    => $config['swftools']['pdf2swf'],
            'swfrender.binaries'  => $config['swftools']['swfrender'],
            'swfextract.binaries' => $config['swftools']['swfextract'],
            'timeout'             => $config['swftools']['timeout']
        ));

        $container->setParameter('phlexible_media_tool.poppler.configuration', array(
            'pdfinfo.binaries'   => $config['poppler']['pdfinfo'],
            'pdftotext.binaries' => $config['poppler']['pdftotext'],
            'pdftohtml.binaries' => $config['poppler']['pdftohtml'],
            'timeout'            => $config['poppler']['timeout'],
        ));

        $container->setParameter('phlexible_media_tool.ffmpeg.configuration', array(
            'ffprobe.binaries' => $config['ffmpeg']['ffprobe'],
            'ffmpeg.binaries'  => $config['ffmpeg']['ffmpeg'],
        ));

        $container->setParameter('phlexible_media_tool.mime.file', $config['mime']['file']);
        $container->setParameter('phlexible_media_tool.mime.magicfile', $config['mime']['magicfile']);

        $container->setAlias('phlexible_media_tool.image_analyzer.driver', $config['image_analyzer']['driver']);

        $container->setAlias('phlexible_media_tool.mime.adapter', $config['mime_detector']['adapter']);
    }
}
