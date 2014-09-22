<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\ElementStructure\Diff\Differ;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update usage command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateUsageCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elements:update-usage')
            ->setDescription('Update usage for element.')
            ->addArgument('eid', InputArgument::REQUIRED, 'EID');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileUsageUpdate = $this->getContainer()->get('phlexible_element.file_usage_updater');
        $folderUsageUpdate = $this->getContainer()->get('phlexible_element.folder_usage_updater');

        $eid = $input->getArgument('eid');

        $fileUsageUpdate->updateUsage($eid);
        $folderUsageUpdate->updateUsage($eid);

        return 0;
    }
}

