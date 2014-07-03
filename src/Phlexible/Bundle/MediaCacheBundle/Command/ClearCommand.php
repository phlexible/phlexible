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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClearCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:clear')
            ->setDescription('Clear waiting cache items');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueRepository = $this->getContainer()->get('mediacache.queue.repository');

        $cnt = $queueRepository->deleteAll();

        $output->writeln('Deleted ' . $cnt . ' items.');

        return 0;
    }

}
