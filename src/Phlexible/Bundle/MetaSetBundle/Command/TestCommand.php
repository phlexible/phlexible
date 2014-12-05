<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Command;

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
            ->setName('metaset:test')
            ->setDescription('test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $d = $this->getContainer()->get('phlexible_meta_set.doctrine.meta_set_manager');
        $f = $this->getContainer()->get('phlexible_meta_set.file.meta_set_manager');
        $um = $this->getContainer()->get('phlexible_user.user_manager');

        foreach ($d->findAll() as $metaSet) {
            $metaSet->setRevision(1);
            $metaSet->setCreateUser($um->find($metaSet->getCreateUser())->getDisplayName());
            $metaSet->setModifyUser($um->find($metaSet->getModifyUser())->getDisplayName());
            echo $metaSet->getCreateUser();
            $f->updateMetaSet($metaSet);
        }

        return 0;
    }

}
