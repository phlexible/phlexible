<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ShowCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('siteroot:show')
            ->setDescription('Show siteroot infos.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siterootManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');

        foreach ($siterootManager->findAll() as $siteroot) {
            $output->write('<info>'.$siteroot->getTitle('en').'</info>');

            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('');
                $output->writeln('  ID: '.$siteroot->getId());
                if ($siteroot->getUrls()) {
                    $output->writeln('  Urls:');
                    foreach ($siteroot->getUrls() as $url) {
                        $output->writeln('    '.$url->getHostname().' => '.$url->getTarget());
                    }
                }

                if ($siteroot->getSpecialTids()) {
                    $output->writeln('  Special TIDs:');
                    foreach ($siteroot->getSpecialTids() as $specialTid) {
                        $name = $specialTid['name'];
                        $value = ($specialTid['language'] ? $specialTid['language'].':' : '').$specialTid['treeId'];
                        $output->writeln("    $name => $value");
                    }
                }
            } else {
                $output->writeln(': '.$siteroot->getId());
            }
        }

        return 0;
    }
}
