<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flush command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FlushCommand extends  ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:flush')
            ->setDefinition(array(
                new InputOption('really', null, InputOption::VALUE_NONE, 'Really delete'),
            ))
            ->setDescription('Flush temp and cache files')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('really'))
        {
            $output->writeln('Are you sure you want to flush all temp and cache files?');
            $output->writeln('Depending of the amount of files and media templates recreating these could take a _long_ time.');
            $output->writeln('If you are sure, provide the --really switch');

            return 1;
        }

        $output->writeln('Seriously?');

        return 0;
    }

}
