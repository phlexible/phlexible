<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->setName('tree:dump')
            ->setDescription('Dump tree.')
            ->addArgument('siterootId', InputArgument::REQUIRED, 'Siteroot ID');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siterootId = $input->getArgument('siterootId');

        $treeManager = $this->getContainer()->get('phlexible_tree.tree_manager');
        $siterootManager = $this->getContainer()->get('phlexible_siteroot.siteroot_manager');
        $dumper = $this->getContainer()->get('phlexible_tree.content_tree_dumper');

        $tree = $treeManager->getBySiteRootId($siterootId);
        if (!$tree) {
            $output->writeln("<error>Tree for siteroot ID $siterootId not found.</error>");

            return 1;
        }

        $siteroot = $siterootManager->find($siterootId);
        if (!$siteroot) {
            $output->writeln("<error>Siteroot with ID $siterootId not found.</error>");

            return 1;
        }

        $xmlDir = $this->getContainer()->getParameter('phlexible_tree.content.xml_dir');
        $dumper->dump($tree, $siteroot, $xmlDir . $siteroot->getId() . '.xml');

        return 0;
    }
}

