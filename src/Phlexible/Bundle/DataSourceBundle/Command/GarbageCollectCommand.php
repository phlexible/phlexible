<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to start garbage collection.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class GarbageCollectCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('datasource:garbage-collect')
            ->setDescription('Cleanup unused data source values');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gc = $this->getContainer()->get('phlexible_data_source.garbage_collector');

        $stats = $gc->run();

        $cntActivated   = $stats['activated'];
        $cntDeactivated = $stats['deactivated'];
        $cntRemoved     = $stats['removed'];

        $output->writeln(
            "Garbage collect has activated $cntActivated, "
            . "deactivated $cntDeactivated "
            . "and removed $cntRemoved values."
        );

        return 0;
    }

}
