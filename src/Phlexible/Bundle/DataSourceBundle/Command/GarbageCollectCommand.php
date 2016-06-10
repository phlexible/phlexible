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
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Cleanup unused data source values')
            ->addOption('run', null, InputOption::VALUE_NONE, 'Execute. Otherwise only stats are shown.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gc = $this->getContainer()->get('phlexible_data_source.garbage_collector');

        $pretend = !$input->getOption('run');
        $stats = $gc->run($pretend);

        foreach ($stats as $name => $langs) {
            foreach ($langs as $lang => $parts) {
                $cntActivated = !empty($parts['activate']) ? count($parts['activate']) : 0;
                $cntDeactivated = !empty($parts['inactivate']) ? count($parts['inactivate']) : 0;
                $cntRemoved = !empty($parts['remove']) ? count($parts['remove']) : 0;

                if ($pretend) {
                    $output->writeln(
                        "Garbage collect run will activate $cntActivated, "
                        . "deactivate $cntDeactivated "
                        . "and remove $cntRemoved values on data source $name."
                    );
                } else {
                    $output->writeln(
                        "Garbage collect run has activated $cntActivated, "
                        . "deactivated $cntDeactivated "
                        . "and removed $cntRemoved values on data source $name."
                    );
                }
            }
        }

        return 0;
    }

}
