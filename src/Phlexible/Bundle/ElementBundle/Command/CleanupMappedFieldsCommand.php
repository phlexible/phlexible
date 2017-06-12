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

use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanupMappedFieldsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:cleanup:mapped-fields')
            ->setDescription('Cleanup mapped fields for elements.')
            ->addArgument('eid', InputArgument::OPTIONAL, 'EID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $elementManager = $this->getContainer()->get('phlexible_element.element_manager');
        $elementVersionManager = $this->getContainer()->get('phlexible_element.element_version_manager');
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->getConfiguration()->setSQLLogger(null);
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $mappedFieldRepository = $entityManager->getRepository(ElementVersionMappedField::class);

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

                $languages = [];
                foreach ($mappedFieldRepository->findBy(['elementVersion' => $elementVersion], ['id' => 'DESC']) as $mappedField) {
                    if (!isset($languages[$mappedField->getLanguage()])) {
                        $languages[$mappedField->getLanguage()] = true;
                    } else {
                        $entityManager->remove($mappedField);
                    }
                }

                #$elementVersionManager->updateElementVersion($elementVersion, false);
            }

            if ($index % 10 === 0) {
                $entityManager->flush();
                $entityManager->clear();
                gc_collect_cycles();
            }
        }

        $entityManager->flush();
        $entityManager->clear();
        gc_collect_cycles();

        $style->writeln('');
        $style->writeln('Done');

        return 0;
    }
}
