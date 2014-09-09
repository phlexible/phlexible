<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test command
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('teasers:test')
            ->setDescription('Test teasers.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();

        ini_set('memory_limit', -1);

        $catchRepository = $this->getContainer()->get('phlexible_teaser.teaser_service');
        $catcher = $this->getContainer()->get('phlexible_teaser.catcher');

        $catch = $catchRepository->find(50);
        $resultPool = $catcher->catchElements(
            $catch,
            array('de'),
            false,
            new \Brainbits_Teasers_Catch_Filter_References()
        );

        ldd($resultPool->getFilteredItems(array('sector' => 'healthcare')));
    }
}
