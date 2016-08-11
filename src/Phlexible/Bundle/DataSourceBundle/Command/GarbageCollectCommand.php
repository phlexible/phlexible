<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Command;

use Phlexible\Bundle\DataSourceBundle\DataSourceMessage;
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
        $messagePoster = $this->getContainer()->get('phlexible_message.message_poster');

        $pretend = !$input->getOption('run');
        $stats = $gc->run(0, $pretend);

        $subjects = array();

        foreach ($stats as $name => $langs) {
            foreach ($langs as $lang => $values) {
                $cntActivate = $values->countActiveValues();
                $cntInactive = $values->countInactiveValues();
                $cntRemove = $values->countRemoveValues();

                if ($pretend) {
                    $output->writeln(
                        "[$name, $lang] Would store $cntActivate active, "
                        ."store $cntInactive inactive "
                        ."and remove $cntRemove values"
                    );

                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        $output->writeln(' Active: '.json_encode($values->getActiveValues()));
                        $output->writeln(' Inactive: '.json_encode($values->getInactiveValues()));
                        $output->writeln(' Remove: '.json_encode($values->getRemoveValues()));
                    }
                } else {
                    $subject = "[$name, $lang] Stored $cntActivate active, "
                        ."stored $cntInactive inactive "
                        ."and removed $cntRemove values";

                    $output->writeln($subject);

                    if ($cntActivate || $cntInactive || $cntRemove) {
                        $subjects[] = $subject;
                    }
                }
            }
        }

        if (!$pretend && count($subjects)) {
            $message = DataSourceMessage::create(
                'Garbage collection run on '.count($stats).' data sources.',
                implode(PHP_EOL, $subjects)
            );
            $messagePoster->post($message);
        }

        return 0;
    }

}
