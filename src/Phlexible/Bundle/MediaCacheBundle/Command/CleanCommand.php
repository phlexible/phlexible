<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\LockHandler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Clean command.
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
            ->setDefinition(
                [
                    new InputOption('pretend', null, InputOption::VALUE_NONE, 'Only show files that will be deleted'),
                ]
            )
            ->setDescription('Remove obsolete cache files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $lock = new LockHandler('mediacache_lock', $container->getParameter('app.lock_dir'));
        if (!$lock->lock()) {
            throw new AlreadyRunningException('Another media cache process is running.');
        }

        $pretend = $input->getOption('pretend');

        $cacheManager = $container->get('phlexible_media_cache.cache_manager');
        $storageManager = $this->getContainer()->get('phlexible_media_cache.storage_manager');
        $templateManager = $this->getContainer()->get('phlexible_media_template.template_manager');
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $files = [];
        foreach ($storageManager->all() as $storage) {
            $finder = Finder::create()
                ->in(rtrim($storage->getStorageDir(), '/'))
                ->files();
            foreach ($finder as $file) {
                /* @var $file SplFileInfo */
                $files[$file->getPathname()] = 1;
            }
        }

        $offset = 0;
        $limit = 50;
        while ($cacheItems = $cacheManager->findBy(array(), null, $limit, $offset)) {
            foreach ($cacheItems as $cacheItem) {
                /* @var $cacheItem CacheItem */
                $template = $templateManager->find($cacheItem->getTemplateKey());
                $storage = $storageManager->get($template->getStorage());
                $path = $storage->getLocalPath($cacheItem);
                if (isset($files[$path])) {
                    unset($files[$path]);
                }
                if (!$template->getManaged()) {
                    if (!$pretend) {
                        $cacheManager->deleteCacheItem($cacheItem);
                    } else {
                        $output->writeln("Delete unmanaged cache item {$cacheItem->getId()} / file {$cacheItem->getFileId()} / template {$template->getKey()}");
                    }
                }
            }
            $offset += $limit;
            $em->clear();
        }

        if (count($files)) {
            $filesystem = new Filesystem();

            foreach (array_keys($files) as $file) {
                if ($pretend || $output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) {
                    $output->writeln('Delete file: '.$file);
                }

                if (!$pretend) {
                    $filesystem->remove($file);
                }
            }

            $output->writeln(count($files).' obsolete cache files.');
        } else {
            if ($pretend || $output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) {
                $output->writeln('No obsolete cache files.');
            }
        }

        $lock->release();

        return 0;
    }
}
