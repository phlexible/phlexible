<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\MediaCacheBundle\Commit\TemplateChangeCommitter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commit command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChangesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:changes')
            ->setDefinition(array(
                new InputOption('commit', null, InputOption::VALUE_NONE, 'Commit changes'),
            ))
            ->setDescription('Show media template changes')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $committer = new TemplateChangeCommitter(
            $container->get('mediatemplates.repository'),
            $container->get('mediacache.repository'),
            $container->get('mediacache.queue')
        );

        $changes = $committer->changes();

        if (count($changes)) {
            foreach ($changes as $change) {
                $output->writeln(
                    'FILE ' . $change['fileId'] . ':' . $change['fileVersion'] . ' ' .
                    'TEMPLATE ' . $change['template']->getKey() . ' ' .
                    'REVISION ' . $change['fileRevision'] . ' => ' . $change['template']->getRevision()
                );
            }

            if ($input->getOption('commit')) {
                $committer->commit();
            }
        } else {
            $output->writeln('No media template changes');
        }

        return 0;
    }
}
