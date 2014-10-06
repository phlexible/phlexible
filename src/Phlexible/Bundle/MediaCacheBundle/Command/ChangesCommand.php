<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Bundle\MediaCacheBundle\Change\TemplateChanges;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setDefinition(
                array(
                    new InputOption('commit', null, InputOption::VALUE_NONE, 'Commit changes'),
                    new InputOption('queue', null, InputOption::VALUE_NONE, 'Via queue'),
                )
            )
            ->setDescription('Show media template changes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $committer = new TemplateChanges(
            $this->getContainer()->get('phlexible_media_template.template_manager'),
            $this->getContainer()->get('phlexible_media_cache.cache_manager'),
            $this->getContainer()->get('phlexible_media_site.site_manager'),
            $this->getContainer()->get('phlexible_media_cache.queue.batch_builder'),
            $this->getContainer()->get('phlexible_media_cache.queue.batch_resolver'),
            $this->getContainer()->get('phlexible_media_cache.queue_manager'),
            $this->getContainer()->get('phlexible_media_cache.queue.worker')
        );

        $changes = $committer->changes();

        if (count($changes)) {
            foreach ($changes as $change) {
                $output->writeln(
                    'FILE ' . $change->getFile()->getId() . ':' . $change->getFile()->getVersion() . ' ' .
                    'TEMPLATE ' . $change->getTemplate()->getKey() . ' ' .
                    'REVISION ' . $change->getRevision() . ' => ' . $change->getTemplate()->getRevision()
                );
            }

            if ($input->getOption('commit')) {
                $committer->commit($input->getOption('queue'));
            }
        } else {
            $output->writeln('No media template changes');
        }

        return 0;
    }
}
