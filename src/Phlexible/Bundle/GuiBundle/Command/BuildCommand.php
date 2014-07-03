<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Command;

use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build assets command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BuildCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('assets:build')
            ->setDescription('Build cached assets');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $basePath = '/cms';
        $baseUrl = $basePath . '/index' . ($container->getParameter('kernel.debug') ? '_dev' : '') . '.php';
        $languages = array('de', 'en');

        $formatter = new FilesizeFormatter();

        $time = microtime(true);
        $scripts = $container->get('resourcesScripts');
        $scripts->get();
        $time = microtime(true) - $time;
        $output->writeln('Scripts built in ' . number_format($time, 2) . 's, filesize ' . $formatter->formatFilesize(filesize($scripts->getCacheFilename()), 2));

        $time = microtime(true);
        $styles = $container->get('resourcesStyles');
        $styles->get($baseUrl, $basePath);
        $time = microtime(true) - $time;
        $output->writeln('Styles built in ' . number_format($time, 2) . 's, filesize ' . $formatter->formatFilesize(filesize($styles->getCacheFilename()), 2));

        $time = microtime(true);
        $sprite = $container->get('resourcesSprite');
        $sprite->get($basePath, $basePath);
        $time = microtime(true) - $time;
        $output->writeln('Sprite built in ' . number_format($time, 2) . 's, filesize ' . $formatter->formatFilesize(filesize($sprite->getCacheFilename()), 2));

        $translations = $container->get('resourcesTranslations');
        foreach ($languages as $language) {
            $time = microtime(true);
            $translations->get($language);
            $time = microtime(true) - $time;
            $output->writeln('Translations ' . $language.' built in ' . number_format($time, 2) . 's, filesize ' . $formatter->formatFilesize(filesize($translations->getCacheFilename($language)), 2));
        }

        return 0;
    }

}
