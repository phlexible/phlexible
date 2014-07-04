<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Brainbits\Mime\MimeDetector;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:test')
            ->setDefinition(
                array(
                    new InputArgument('file-id', InputArgument::REQUIRED, 'File ID'),
                )
            )
            ->setDescription('Test');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileId = $input->getArgument('file-id');

        $metaSetManager = $this->getContainer()->get('phlexible_meta_set.meta_set_manager');
        $fileMetaManager = $this->getContainer()->get('phlexible_media_manager.file_meta_data_manager');
        $siteManager = $this->getContainer()->get('phlexible_media_site.manager');
        $file = $siteManager->getByFileId($fileId)->findFile($fileId);

        $metaSetIds = $file->getAttribute('metasets');
        foreach ($metaSetIds as $metaSetId) {
            $metaSet = $metaSetManager->find($metaSetId);
            if (!$metaSet) {
                throw new \Exception("Meta set $metaSetId not found.");
            }
            $output->writeln($metaSet->getName());
            $metaData = $fileMetaManager->findByMetaSetAndIdentifiers($metaSet, array('file_id' => $file->getId(), 'file_version' => $file->getVersion()));
            ld($metaData);
        }

        return 0;
    }
}
