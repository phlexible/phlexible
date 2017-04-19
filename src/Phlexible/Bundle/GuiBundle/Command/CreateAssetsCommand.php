<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Command;

use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Assets command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateAssetsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('gui:build-assets')
            ->setDescription('Build phlexible gui assets')
            ->addOption('base-path', null, InputOption::VALUE_REQUIRED, 'Request base path')
            ->addOption('language', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Language for translations');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = new FilesizeFormatter();

        $scriptsBuilder = $this->getContainer()->get('phlexible_gui.asset.scripts_builder');
        $scriptAsset = $scriptsBuilder->build();

        $output->writeln(basename($scriptAsset->getFile()).': '.$formatter->formatFilesize(filesize($scriptAsset->getFile())));

        $cssBuilder = $this->getContainer()->get('phlexible_gui.asset.css_builder');
        $cssAsset = $cssBuilder->build();

        $output->writeln(basename($cssAsset->getFile()).': '.$formatter->formatFilesize(filesize($cssAsset->getFile())));

        $basePath = $input->getOption('base-path') ?? '';

        $iconsBuilder = $this->getContainer()->get('phlexible_gui.asset.icons_builder');
        $iconsAsset = $iconsBuilder->build($basePath);

        $output->writeln(basename($iconsAsset->getFile()).': '.$formatter->formatFilesize(filesize($iconsAsset->getFile())));

        $languages = $input->getOption('language');

        if ($languages) {
            foreach ($languages as $language) {
                $translationBuilder = $this->getContainer()->get('phlexible_gui.asset.translations_builder');
                $translationAsset = $translationBuilder->build($language);

                $output->writeln(basename($translationAsset->getFile()).': '.$formatter->formatFilesize(filesize($translationAsset->getFile())));
            }
        }
    }
}
