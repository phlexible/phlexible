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

        $output->writeln($scriptAsset->getBasename().': '.$formatter->formatFilesize($scriptAsset->getSize()));
        $output->writeln($scriptAsset->getPathname());

        $cssBuilder = $this->getContainer()->get('phlexible_gui.asset.css_builder');
        $cssAsset = $cssBuilder->build();

        $output->writeln($cssAsset->getBasename().': '.$formatter->formatFilesize($cssAsset->getSize()));
        $output->writeln($cssAsset->getPathname());

        $basePath = $input->getOption('base-path') ?: '';

        $iconsBuilder = $this->getContainer()->get('phlexible_gui.asset.icons_builder');
        $iconsAsset = $iconsBuilder->build($basePath);

        $output->writeln($iconsAsset->getBasename().': '.$formatter->formatFilesize($iconsAsset->getSize()));
        $output->writeln($iconsAsset->getPathname());

        $languages = $input->getOption('language');

        if ($languages) {
            foreach ($languages as $language) {
                $translationBuilder = $this->getContainer()->get('phlexible_gui.asset.translations_builder');
                $translationAsset = $translationBuilder->build($language);

                $output->writeln($translationAsset->getBasename().': '.$formatter->formatFilesize($translationAsset->getSize()));
                $output->writeln($translationAsset->getPathname());
            }
        }
    }
}
