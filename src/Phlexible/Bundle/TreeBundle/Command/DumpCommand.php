<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Phlexible\Bundle\TreeBundle\ContentTree\Dumper\XmlDumper;
use Phlexible\Bundle\TreeBundle\ContentTree\XmlContentTree;
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

        $treeManager = $this->getContainer()->get('phlexible_tree.manager');
        $siterootRepository = $this->getContainer()->get('phlexible_siteroot.repository');

        $tree = $treeManager->getBySiteRootId($siterootId);
        $siteroot = $siterootRepository->find($siterootId);

        $dumper = new XmlDumper($this->getContainer()->get('phlexible_element.service'));
        $dumper->dump($tree, $siteroot, '/tmp/test.xml');

        $loadedTree = new XmlContentTree('/tmp/test.xml');
        print_r($loadedTree->getSpecialTids());
        print_r($loadedTree->getUrls());
        die;
        $root = $loadedTree->getRoot();
        echo $root->getId() . PHP_EOL;
        foreach ($loadedTree->getChildren($root) as $node) {
            echo ' ' . $node->getId() . PHP_EOL;
            foreach ($loadedTree->getChildren($node) as $subNode) {
                echo '  ' . $subNode->getId() . PHP_EOL;
            }
        }
        echo $loadedTree->isParentOf(1086, 1088) . PHP_EOL;
        echo $loadedTree->isParentOf(1088, 1086) . PHP_EOL;
        echo $loadedTree->isChildOf(1088, 1086) . PHP_EOL;
        echo $loadedTree->isChildOf(1086, 1088) . PHP_EOL;
        echo $loadedTree->isParentOf(1, 1088) . PHP_EOL;

        return 0;
    }
}

