<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateLinksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:generate:links')
            ->setDescription('Generate links for elements.')
            ->addArgument('eid', InputArgument::OPTIONAL, 'EID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $elementManager = $this->getContainer()->get('phlexible_element.element_manager');
        $elementStructureManager = $this->getContainer()->get('phlexible_element.element_structure_manager');
        $elementService = $this->getContainer()->get('phlexible_element.element_service');
        $linkUpdater = $this->getContainer()->get('phlexible_element.link_updater');

        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->getConfiguration()->setSQLLogger(null);
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $criteria = array();
        if ($eid = $input->getArgument('eid')) {
            $criteria['eid'] = $eid;
        }

        $elements = $elementManager->findBy($criteria);
        $countElements = count($elements);

        $style->progressStart($countElements);

        foreach ($elements as $index => $element) {
            $elementVersions = $elementService->findElementVersions($element);
            $countElementVersions = count($elementVersions);

            $style->progressAdvance();
            $style->write(" ".(memory_get_usage(true)/1024/1024)." mb | Element {$element->getEid()} ({$countElementVersions} Versions) ");

            foreach ($elementVersions as $elementVersion) {
                $style->write(
                    '.'
                );

                try {
                    $elementStructure = $elementStructureManager->find($elementVersion);
                    $elementStructure->setElementVersion($elementVersion);

                    $linkUpdater->updateLinks($elementStructure, false, true);
                } catch (\Exception $e) {
                    $style->error($e->getMessage());
                }
            }

            if ($index % 10 === 0) {
                $elementStructureManager->clear();
                $entityManager->flush();
                $entityManager->clear();
                gc_collect_cycles();
            }
        }

        $elementStructureManager->clear();
        $entityManager->flush();
        $entityManager->clear();
        gc_collect_cycles();

        $style->writeln('');
        $style->writeln('Done');

        return 0;
    }
}
