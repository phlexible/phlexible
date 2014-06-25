<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Brainbits\Mime\MimeDetector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:read')
            ->setDefinition(array(
                new InputArgument('folderId', InputArgument::OPTIONAL, 'Folder ID'),
            ))
            ->setDescription('Re-read all metadata')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $folderId = $input->getArgument('folderId');

        $mimeDetector = $this->getContainer()->get('mediatools.mime.detector');
        $documenttypeRepository = $this->getContainer()->get('documenttypes.repository');

        if ($folderId) {
            $site   = $this->getContainer()->get('mediasite.manager')->getByFolderId($folderId);
            $folder = $site->getFolderPeer()->getById($folderId);

            $cnt = $folder->reRead();

            $output->writeln($cnt . ' files read.');
        } else {
            $sites = $this->getContainer()->get('mediasite.manager')->getAll();

            foreach ($sites as $site) {
                /* @var $site SiteInterface */
                $folder = $site->findRootFolder();

                $rii = new \RecursiveIteratorIterator(new FolderIterator($site), \RecursiveIteratorIterator::SELF_FIRST);
                foreach ($rii as $folder) {
                    $output->writeln('Folder ' . $folder->getName());
                    foreach ($site->findFilesByFolder($folder) as $file) {
                        $output->writeln('File ' . $file->getName());

                        $mimetype = $mimeDetector->detect($file->getPhysicalPath(), MimeDetector::RETURN_STRING);
                        if (!$mimetype) {
                            $output->writeln("File not found. skipping");

                            $attributes = $file->getAttributes();
                            $attributes['mimetype'] = 'application/octet-stream';
                            $attributes['documenttype'] = 'binary';
                            $attributes['assettype'] = 'DOCUMENT';

                            $site->setFileAttributes($file, $attributes);

                            continue;
                        }

                        $documenttype = $documenttypeRepository->findByMimetype($mimetype);

                        $attributes = $file->getAttributes();
                        if (!empty($attributes['documentType'])) {
                            unset($attributes['documentType']);
                        }
                        if (!empty($attributes['assetType'])) {
                            unset($attributes['assetType']);
                        }

                        $attributes['mimetype'] = $mimetype;
                        $attributes['documenttype'] = $documenttype->getKey();
                        $attributes['assettype'] = $documenttype->getType();

                        $site->setFileAttributes($file, $attributes);
                    }
                }
            }

        }

        return 0;
    }
}
