<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Phlexible\Bundle\ElementBundle\Change\AddChange;
use Phlexible\Bundle\ElementBundle\Change\Checker;
use Phlexible\Bundle\ElementBundle\Change\RemoveChange;
use Phlexible\Bundle\ElementBundle\Change\UpdateChange;
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
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force update')
            ->addOption('sync', null, InputOption::VALUE_NONE, 'Synchronize changes.')
            ->addOption('filter', null, InputOption::VALUE_REQUIRED, 'Filter changes. Specify "add", "update" or "remove".')
            ->addOption('queue', null, InputOption::VALUE_NONE, 'Via queue');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checker = $this->getContainer()->get('phlexible_element.checker');
        $synchronizer = $this->getContainer()->get('phlexible_element.synchronizer');

        $sync = $input->getOption('sync');
        $filter = $input->getOption('filter');
        $force = $input->getOption('force');

        $changes = $checker->check($force);

        if (count($changes)) {
            if ($filter === 'add') {
                $changes = $changes->filterAdd();
            } elseif ($filter === 'update') {
                $changes = $changes->filterUpdate();
            } elseif ($filter === 'remove') {
                $changes = $changes->filterRemove();
            } elseif ($filter) {
                $output->writeln("Invalid filter option $filter");

                return 1;
            }

            if (!$sync) {
                $table = new Table($output);
                $table->setHeaders([
                    'Elementtype',
                    'Type',
                    'New Revision',
                    'Old Revisions',
                    'Change',
                    '# Element sources',
                    '# Element usages',
                ]);

                foreach ($changes as $change) {
                    $oldRevisions = array();
                    $color = 'white';
                    if ($change instanceof RemoveChange) {
                        $oldRevisions = [];
                        foreach ($change->getRemovedElementSources() as $targetElementSource) {
                            $oldRevisions[] = $targetElementSource->getElementtypeRevision();
                        }
                        $color = 'red';
                    } elseif ($change instanceof UpdateChange) {
                        $oldRevisions = [];
                        foreach ($change->getOutdatedElementSources() as $targetElementSource) {
                            $oldRevisions[] = $targetElementSource->getElementtypeRevision();
                        }
                        $color = 'yellow';
                    } elseif ($change instanceof AddChange) {
                        $color = 'green';
                    }
                    $name = $change->getElementtype()->getUniqueId();
                    $type = $change->getElementtype()->getType();
                    $revision = $change->getElementtype()->getRevision();

                    $table->addRow(
                        [
                            $name,
                            $type,
                            $revision,
                            count($oldRevisions) ? implode(',', $oldRevisions) : '-',
                            "<fg=$color>{$change->getReason()}</fg=$color>",
                            count($oldRevisions) ?: '-',
                            count($change->getUsage()) ?: '-'
                        ]
                    );
                }

                $table->render();
            } else {
                foreach ($changes as $change) {
                    $ts = microtime(true);
                    $output->writeln("Synchronizing change for {$change->getElementtype()->getUniqueId()}... ");
                    $synchronizer->synchronize($change, $input->getOption('force'));
                    $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
                    $ts = number_format(microtime(true) - $ts, 2);
                    $output->writeln("<info>... OK ($ts s)</info>");
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

