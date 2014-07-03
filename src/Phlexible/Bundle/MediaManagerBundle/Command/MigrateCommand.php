<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migrate command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MigrateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:migrate')
            ->setDefinition(
                array(
                    new InputArgument('site', InputArgument::OPTIONAL, 'Site to migrate'),
                    new InputOption('pretend', null, InputOption::VALUE_NONE, 'Simulate migration'),
                )
            )
            ->setDescription('Migrate a site from Db to Db2');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteKey = $input->getArgument('site');
        $pretend = $input->getOption('pretend');

        $site = $this->getContainer()->get('mediasite.manager')->get($siteKey);

        if (get_class($site) !== 'Media_SiteDb_Site') {
            return 'Site has to be Media_SiteDb_Site.' . PHP_EOL;
        }

        $db = $this->getContainer()->dbPool->default;

        $root = $site->getFolderPeer()->getRoot();

        $this->migrateFolder($root, $pretend);

        if (!$pretend) {
            $db->update(
                $db->prefix . 'mediamanager_site',
                array('driver' => 'Media_SiteDb2_Site'),
                $db->quoteIdentifier('key') . ' = ' . $db->quote($siteKey)
            );
        }

        return 0;
    }

    protected function migrateFolder(Folder $folder, $pretend = true)
    {
        echo $folder->getPhysicalPath() . PHP_EOL;
        if ($folder->hasFiles()) {
            foreach ($folder->getFiles() as $file) {
                $id = $file->getId();
                $source = $file->getFilePath();
                $targetDir = $file->getSite()->getRootDir() . $id[0] . '/' . $id[0] . $id[1] . '/' . $id . '/';
                $target = $targetDir . '1';

                if (!file_exists($source)) {
                    echo 'skipping "' . $source . '", file not found' . PHP_EOL;
                    continue;
                }

                if (!$pretend) {
                    mkdir($targetDir, 0777, true);
                }
                //                echo 'cp ('.$source.', '.$target.')'.PHP_EOL;
                if (!$pretend) {
                    copy($source, $target);
                }
            }
        }

        if ($folder->hasSubFolders()) {
            foreach ($folder->getFolders() as $childFolder) {
                $this->migrateFolder($childFolder, $pretend);
            }
        }
    }
}
