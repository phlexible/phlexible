<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Change\Checker;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
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
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force import')
            ->addOption('commit', null, InputOption::VALUE_NONE, 'Commit changes')
            ->addOption('queue', null, InputOption::VALUE_NONE, 'Via queue');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checker = new Checker(
            $this->getContainer()->get('phlexible_elementtype.elementtype_service'),
            $this->getContainer()->get('phlexible_element.element_service'),
            $this->getContainer()->get('phlexible_element.element_source_manager')
        );
        $synchronizer = $this->getContainer()->get('phlexible_element.synchronizer');

        $changes = $checker->check();

        if (count($changes)) {
            if (!$input->getOption('commit')) {
                $table = new Table($output);
                $table->setHeaders([
                    'Elementtype',
                    'New Revision',
                    'Old Revisions',
                    'Needs import?',
                    '# Element source updates'
                    ]);

                foreach ($changes as $change) {
                    $oldRevisions = [];
                    foreach ($change->getOutdatedElementSources() as $outdatedElementSource) {
                        $oldRevisions[] = $outdatedElementSource->getElementtypeRevision();
                    }
                    $table->addRow(
                        [
                            $change->getElementtype()->getTitle(),
                            $change->getElementtype()->getRevision(),
                            implode(',', $oldRevisions),
                            $change->getNeedImport() ? '<fg=green>' . $change->getReason() . '</fg=green>': '-',
                            count($change->getOutdatedElementSources()) ?: '-'
                        ]
                    );
                }

                $table->render();
            } else {
                foreach ($changes as $change) {
                    $output->write("{$change->getElementtype()->getTitle()}... ");
                    $synchronizer->synchronize($change, $input->getOption('force'));
                    $output->writeln("<info>ok</info>");
                }
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

