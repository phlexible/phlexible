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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Init command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InitCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:init')
            ->setDefinition(
                array(
                    new InputArgument('siteName', InputArgument::REQUIRED, 'Site name'),
                )
            )
            ->setDescription('Initialise filesystem');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteName = $input->getArgument('siteName');

        $siteManager = $this->getContainer()->get('phlexible_media_site.site_manager');
        $userManager = $this->getContainer()->get('phlexible_user.user_manager');

        $site = $siteManager->get($siteName);

        $site->createFolder(null, 'root', new AttributeBag(), $userManager->getSystemUserId());

        return 0;
    }
}
