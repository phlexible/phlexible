<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update usage command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateUsageCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('frontend-media:update-usage')
            ->setDescription('Update usage for element.')
            ->addArgument('eid', InputArgument::OPTIONAL, 'EID');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usageUpdater = $this->getContainer()->get('phlexible_frontend_media.usage_updater');
        $elementManager = $this->getContainer()->get('phlexible_element.element_manager');

        $eid = $input->getArgument('eid');
        if ($eid) {
            $element = $elementManager->find($eid);
            if (!$element) {
                $output->write('Removing usage of Element '.$eid.' ... ');
                try {
                    $usageUpdater->removeUsage($eid);
                    $output->writeln('<info>done</info>');
                } catch (\Exception $e) {
                    $output->writeln('<error>'.$e->getMessage().'</error>');
                }
                return 0;
            }
            $output->write('Updating Element '.$element->getEid().' ... ');
            try {
                $usageUpdater->updateUsage($element);
                $output->writeln('<info>done</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }

        } else {
            $usageUpdater->removeObsolete();
            $offset = 0;
            $limit = 100;
            $elements = $elementManager->findBy(array(), null, $limit, $offset);
            do {
                foreach ($elements as $element) {
                    $output->write('Updating Element '.$element->getEid().' ... ');
                    try {
                        $usageUpdater->updateUsage($element);
                        $output->writeln('<info>done</info>');
                    } catch (\Exception $e) {
                        $output->writeln('<error>'.$e->getMessage().'</error>');
                    }
                }
                $offset += $limit;
                $elements = $elementManager->findBy(array(), null, $limit, $offset);
            } while (count($elements));
        }


        return 0;
    }
}

