<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Brainbits_Util_FileLock as FileLock;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clean command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CleanCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:clean')
            ->setDefinition(array(
                new InputOption('pretend', null, InputOption::VALUE_NONE, 'Only show files that will be deleted'),
            ))
            ->setDescription('Remove obsolete cache files')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $lock = new FileLock('mediacache_lock', $container->getParameter('app.lock_dir'));
        if (!$lock->tryLock())
        {
            throw new AlreadyRunningException('Another media cache process is running.');
        }

        $files = array();
        $sites = $container->get('mediasite.manager')->getAll();
        foreach ($sites as $site) {
            $files = array_merge($files, $this->_getFiles($site));
        }

        $db = $container->dbPool->default;

        $select = $db->select()
            ->FROM($db->prefix . 'media_cache', 'id');

        $ids = $db->fetchCol($select);

        foreach ($ids as $id)
        {
            unset($files[$id]);
        }

        $pretend = $input->getOption('pretend');

        if (count($files))
        {
            foreach ($files as $file)
            {
                if ($pretend || $output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET)
                {
                    $output->writeln('delete: ' . $file);
                }

                if (!$pretend)
                {
                    if (!is_writable(dirname($file)))
                    {
                         $output->writeln('Skipping, missing write permission on: ' . dirname($file));
                         continue;
                    }

                    if (!unlink($file))
                    {
                        $output->writeln('Delete failed: ' . $file);
                    }
                }
            }

            $output->writeln(count($files) . ' obsolete cache files.');
        }
        else
        {
            if ($pretend || $output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET)
            {
                $output->writeln('No obsolete cache files.');
            }
        }

        $lock->unlock();

        return 0;
    }

    protected function _getFiles(Site $site)
    {
        $filePaths = glob($site->getFrameDir() . '*/*/*');
        $files = array();
        foreach ($filePaths as $key => $file)
        {
            $dummy = explode('/', $file);
            $dummy = array_pop($dummy);
            $dummy = explode('.', $dummy);
            $dummy = array_shift($dummy);
            $files[$dummy] = $file;
        }

        return $files;
    }

}
