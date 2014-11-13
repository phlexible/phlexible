<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sort tree command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SortTreeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:sort')
            ->setDescription('Sort tree.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function call()
    {
        $outputter = $this->getOutputter();

        if (empty($commandArgs->options['tid'])
            && empty($commandArgs->options['siterootid'])
            && empty($commandArgs->options['allsiteroots'])
        ) {
            $outputter->writeln('You have to use either -t or -a or -s.');

            return 1;
        }

        if (!empty($commandArgs->options['tid'])) {
            $tid = $commandArgs->options['tid'];
            $node = Makeweb_Elements_Tree_Manager::getInstance()->getNodeByNodeId($tid);

            $this->sortByNode($node);
        } elseif (!empty($commandArgs->options['siterootid'])) {
            $siterootId = $commandArgs->options['siterootid'];
            $siteroots = [$siterootId => Makeweb_Siteroots_Siteroot_Manager::getInstance()->getById($siterootId)];

            $this->sortBySiteroots($siteroots);
        } else {
            $siteroots = Makeweb_Siteroots_Siteroot_Manager::getInstance()->getAllSiteRoots();

            $this->sortBySiteroots($siteroots);
        }

        return 0;
    }

    protected function sortByNode(Makeweb_Elements_Tree_Node $node)
    {
        $outputter = $this->getOutputter();
        $container = $this->getContainer();

        $sorter = $container->get('elementsTreeSorter');

        try {
            $time1 = microtime(true);

            $sorter->sortNode($node);

            $time2 = microtime(true);
            $time = number_format($time2 - $time1, 2);

            $outputter->writeln('Node "' . $node->getId() . '" sorted in ' . $time . ' s.');
        } catch (Exception $e) {
            MWF_Log::exception($e);
            $outputter->writeln(
                'Siteroot "' . $node->getTree()->getSiteRoot()->getTitle('en') . '" not sorted because of ' . get_class(
                    $e
                ) . ': ' . $e->getMessage()
            );
        }
    }

    protected function sortBySiteroots(array $siteroots)
    {
        $outputter = $this->getOutputter();

        $container = $this->getContainer();
        $treeManager = $container->get('phlexible_tree.tree_manager');
        $db = $container->dbPool->read;
        $sorter = $container->elementsTreeSorter;

        foreach ($siteroots as $siterootId => $siteroot) {
            try {
                $time1 = microtime(true);

                $tree = $treeManager->getBySiteRootId($siterootId, true);

                $sorter->sortTree($tree);

                $time2 = microtime(true);
                $time = number_format($time2 - $time1, 2);

                $outputter->writeln('Siteroot "' . $siteroot->getTitle('en') . '" sorted in ' . $time . ' s.');
            } catch (Exception $e) {
                MWF_Log::exception($e);
                $outputter->writeln(
                    'Siteroot "' . $siteroot->getTitle('en') . '" not sorted because of ' . get_class(
                        $e
                    ) . ': ' . $e->getMessage()
                );
            }
        }

        $db->getProfiler()->setEnabled(false);

        $profiles = $db->getProfiler()->getQueryProfiles();
        foreach ($profiles as $profile) {
            //$outputter->writeln($profile->getElapsedSecs() . ' ' . str_replace("\n", ' ', $profile->getQuery()));
        }
        //$outputter->writeln($db->getProfiler()->getTotalNumQueries());
        //$outputter->writeln($db->getProfiler()->getTotalElapsedSecs());
    }

}

