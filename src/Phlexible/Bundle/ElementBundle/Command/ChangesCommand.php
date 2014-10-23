<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Change\ElementtypeChanges;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Changes command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChangesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:changes')
            ->setDescription('Show element changes.')
            ->addOption('commit', null, InputOption::VALUE_NONE, 'Commit changes')
            ->addOption('queue', null, InputOption::VALUE_NONE, 'Via queue');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $committer = new ElementtypeChanges(
            $this->getContainer()->get('phlexible_elementtype.elementtype_service'),
            $this->getContainer()->get('phlexible_element.element_service'),
            $this->getContainer()->get('phlexible_element.doctrine.synchronizer')
        );

        $changes = $committer->changes();

        if (count($changes)) {
            foreach ($changes as $change) {
                $output->writeln(
                    'ELEMENTTYPE ' . $change->getElementtype()->getTitle() . ' ' .
                    //'REVISION ' . $change->getRevision() . ' => ' . $change->getElementtype()->getRevision() . ' ' .
                    'NUM ELEMENTVERSIONS ' . count($change->getElementVersions())
                );
            }

            if ($input->getOption('commit')) {
                $committer->commit($input->getOption('queue'));
            }
        } else {
            $output->writeln('No elementtype changes');
        }

        return 0;

        // TODO: meta, titles
    }

    /**
     * @param ElementStructure $structure
     *
     * @return ElementStructure
     */
    private function iterateStructure(ElementStructure $structure)
    {
        $elementStructure = new ElementStructure();
        $elementStructure
            ->setId($structure->getId())
            ->setDsId($structure->getDsId())
            ->setName($structure->getName())
            //->setParentId($structure->getParentId())
            //->setParentDsId($structure->getParentDsId())
            ->setParentName($structure->getParentName());
        ;

        foreach ($structure->getValues() as $value) {
            $elementStructure->setValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $elementStructure->addStructure($this->iterateStructure($childStructure));
        }

        return $elementStructure;
    }
}

