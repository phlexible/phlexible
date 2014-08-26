<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Distill command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DistillCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elementtypes:distill')
            ->setDefinition(
                array(
                    new InputArgument('elementtypeId', InputArgument::REQUIRED, 'Element type ID'),
                )
            )
            ->setDescription('Distill element type.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementtypeId = $input->getArgument('elementtypeId');

        $container = $this->getContainer();

        $elementtypeService = $container->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($elementtypeId);
        $elementtypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);
        $elementtypeStructure = $elementtypeService->findElementtypeStructure($elementtypeVersion);

        $rootNode = $elementtypeStructure->getRootNode();
        $data = $this->iterate($output, $elementtypeStructure, $rootNode);
        $output->writeln(print_r($data, 1));

        return 0;
    }

    private function iterate(
        OutputInterface $output,
        ElementtypeStructure $structure,
        ElementtypeStructureNode $node
    )
    {
        $fieldRegistry = $this->getContainer()->get('phlexible_elementtype.field.registry');
        $data = array();

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $fieldRegistry->getField($childNode->getFieldType());

            if ($field->isField()) {
                $data[$childNode->getWorkingTitle()] = true;

                $output->writeln(
                    $childNode->getDsId() . ' ' . $childNode->getTitle() . ' ' . $childNode->getFieldType()
                );
            }

            if ($structure->hasChildNodes($childNode->getDsId())) {
                $childData = $this->iterate($output, $structure, $childNode);

                if ($childNode->isRepeatable() || $childNode->isOptional()) {
                    $data[$node->getWorkingTitle()] = $childData;
                } else {
                    $data = array_merge($data, $childData);
                }
            }
        }

        return $data;
    }
}
