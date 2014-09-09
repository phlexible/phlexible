<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build command
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class BuildCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('teasers:build')
            ->setDescription('Build catch teaser helper data.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $db = $this->getContainer()->dbPool->default;

        $db->beginTransaction();

        $db->delete($db->prefix . 'catchteaser_helper');
        $db->delete($db->prefix . 'catchteaser_metaset_items');

        $select = $db->select()
            ->distinct()
            ->from($db->prefix . 'element_tree', 'eid');

        $eids = $db->fetchCol($select);

        $catchHelper = $this->getContainer()->get('phlexible_teaser.catch.helper');

        foreach ($eids as $eid) {
            $catchHelper->update($eid);
        }

        $db->commit();

        $endTime = microtime(true);
        $timeSpent = number_format(($endTime - $startTime) / 60, 2, ',', '.');

        $mem = number_format(memory_get_peak_usage(true) / 1024 / 1024, 2, ',', '.');

        $output->writeln("Building catchteaser helper tables took $timeSpent minutes and used $mem MB.");

        return $output;
    }
}
