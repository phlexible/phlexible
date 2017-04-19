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
 * Update usage command.
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

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $eid = $input->getArgument('eid');
        if ($eid) {
            $element = $elementManager->find($eid);
            if (!$element) {
                $output->writeln(sprintf(
                    '[<fg=green>%s</>] Removing usage of Element <fg=cyan>%d</> | Memory <fg=cyan>%.2f</> MB',
                    date('Y-m-d H:i:s'),
                    $eid,
                    memory_get_usage(true) / 1024 / 1024
                ));
                try {
                    $usageUpdater->removeUsage($eid);
                } catch (\Exception $e) {
                    $output->writeln('<error>'.$e->getMessage().'</error>');
                }

                return 0;
            }
            $output->writeln(sprintf(
                '[<fg=green>%s</>] Updating Element <fg=cyan>%d</> | Memory <fg=cyan>%.2f</> MB',
                date('Y-m-d H:i:s'),
                $element->getEid(),
                memory_get_usage(true) / 1024 / 1024
            ));
            try {
                $usageUpdater->updateUsage($element);
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
                    $output->writeln(sprintf(
                        '[<fg=green>%s</>] Updating Element <fg=cyan>%d</> | Memory <fg=cyan>%.2f</> MB',
                        date('Y-m-d H:i:s'),
                        $element->getEid(),
                        memory_get_usage(true) / 1024 / 1024
                    ));
                    try {
                        $usageUpdater->updateUsage($element);
                    } catch (\Exception $e) {
                        $output->writeln('<error>'.$e->getMessage().'</error>');
                    }
                }
                $offset += $limit;
                $em->clear();
                $elements = $elementManager->findBy(array(), null, $limit, $offset);
            } while (count($elements));
        }

        return 0;
    }
}
