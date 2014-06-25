<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Write delegates command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WriteDelegatesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:write:delegates')
            ->setDefinition(array(
                new InputOption('force', null, InputOption::VALUE_NONE, 'Force creation, even if not modified.'),
            ))
            ->setDescription('Write delegate thumbs')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');

        $delegateWorker = $this->getContainer()->get('mediacache.image_delegate.worker');

        $delegateWorker->writeAll($force, function() use ($output) {
            $args = func_get_args();
            if ($args[0] === 'count') {
                $this->bar = new ProgressBar($output, $args[1]);
                $this->bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% %message%');
                $this->bar->start();
            } elseif ($args[0] === 'update') {
                $this->bar->setMessage($args[1] . ' / ' . $args[2]);
                $this->bar->advance();
            }
        });

        $output->writeln('');
        $output->writeln('Done.');
    }
}
