<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setDescription('Test');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteManager = $this->getContainer()->get('phlexible_media_site.site_manager');
        $userManager = $this->getContainer()->get('phlexible_user.user_manager');

        $systemUserId = $userManager->getSystemUserId();
        $site = $siteManager->get('test');
        $rootFolder = $site->findRootFolder();

        foreach ($site->findFoldersByParentFolder($rootFolder) as $subFolder) {
            $site->deleteFolder($subFolder, $systemUserId);
        }

        $folder1 = $site->createFolder($rootFolder, 'a', new AttributeBag(), $systemUserId);
        $folder2 = $site->createFolder($folder1, 'bb', new AttributeBag(), $systemUserId);
        $folder3 = $site->createFolder($folder2, 'ccc', new AttributeBag(), $systemUserId);
        $folder4 = $site->createFolder($folder1, 'dd', new AttributeBag(), $systemUserId);
        $folder5 = $site->createFolder($folder4, 'eee', new AttributeBag(), $systemUserId);
        $folder6 = $site->createFolder($folder5, 'ffff', new AttributeBag(), $systemUserId);
        $folder7 = $site->createFolder($folder5, 'gagg', new AttributeBag(), $systemUserId);
        $folder8 = $site->createFolder($rootFolder, 'h', new AttributeBag(), $systemUserId);

        $site->copyFolder($folder1, $folder8, $systemUserId);

        return 0;
    }
}
