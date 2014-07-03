<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stats command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StatsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:stats')
            ->setDescription('Show cache statistics.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Cache Statistics');

        foreach ($this->getContainer()->get('caches') as $name => $cache) {
            $output->writeln("  $name:");

            $stats = $cache->getStats();
            if ($stats !== null) {
                $output->writeln("    Hits             {$stats['hits']}");
                $output->writeln("    Misses           {$stats['misses']}");
                $output->writeln("    Uptime           {$stats['uptime']}");
                $output->writeln("    Memory usage     {$stats['memory_usage']}");
                $output->writeln("    Memory available {$stats['memory_available']}");
            } else {
                $output->writeln("    No stats provided by cache type.");
            }
        }

        return 0;
    }
}
