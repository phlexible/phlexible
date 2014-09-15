<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\ElementStructure\Diff\Differ;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elements:dump')
            ->setDescription('Dump element structure.')
            ->addArgument('eid', InputArgument::REQUIRED, 'Element ID')
            ->addOption('ver', null, InputOption::VALUE_REQUIRED, 'Element version')
            ->addOption('language', null, InputOption::VALUE_REQUIRED, 'Element language')
            ->addOption('values', null, InputOption::VALUE_NONE, 'Show values')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $eid = $input->getArgument('eid');

        $element = $elementService->findElement($eid);

        if ($version = $input->getOption('ver')) {
            $elementVersion = $elementService->findElementVersion($element, $version);
        } else {
            $elementVersion = $elementService->findLatestElementVersion($element);
        }

        $elementStructure = $elementService->findElementStructure($elementVersion, 'de');

        $output->write("<fg=red>Element $eid - Version {$elementVersion->getVersion()}");
        if ($version && $version != $element->getLatestVersion()) {
            $output->write(" - Latest Version {$element->getLatestVersion()}");
        }
        $output->writeln(" - Title {$elementVersion->getBackendTitle('de')}</fg=red>");

        $output->writeln($elementStructure->dump($input->getOption('values'), $input->getOption('language')));

        return 0;
    }
}

