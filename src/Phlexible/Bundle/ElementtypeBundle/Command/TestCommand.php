<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Diff\Differ;
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
            ->setName('elementtypes:test')
            ->setDescription('test.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementtypeService = $this->getContainer()->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype(337);
        $fromElementtypeVersion = $elementtypeService->findElementtypeVersion($elementtype, 200);
        $toElementtypeVersion = $elementtypeService->findElementtypeVersion($elementtype, 400);
        $fromElementtypeStructure = $elementtypeService->findElementtypeStructure($fromElementtypeVersion);
        $toElementtypeStructure = $elementtypeService->findElementtypeStructure($toElementtypeVersion);

        $differ = new Differ();
        $diff = $differ->diff($fromElementtypeStructure, $toElementtypeStructure);

        $output->writeln('Added');
        foreach ($diff->getAdded() as $added) {
            echo '  '.$added->getName()." ".$added->getId().": ".$added->getDsId().PHP_EOL;
        }
        $output->writeln('Moved');
        foreach ($diff->getMoved() as $moved) {
            echo '  '.$moved['newNode']->getName()." ".
                implode('->', $moved['oldNode']->getRepeatableDsIdPath())." => ".
                $moved['oldNode']->getName()." ".
                implode('->', $moved['newNode']->getRepeatableDsIdPath()).PHP_EOL;
        }
        $output->writeln('Removed');
        foreach ($diff->getRemoved() as $removed) {
            echo '  '.$removed->getName()." ".$removed->getId().": ".$removed->getDsId().PHP_EOL;
        }

        return 0;
    }
}

