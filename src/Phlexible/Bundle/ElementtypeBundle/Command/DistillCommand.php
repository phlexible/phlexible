<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Phlexible\Bundle\ElementtypeBundle\Distiller\ClassMap;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
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
            ->setDescription('Distill element type.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementtypeService = $this->getContainer()->get('phlexible_elementtype.elementtype_service');

        $classMap = new ClassMap();
        foreach ($elementtypeService->findAllElementtypes() as $elementtype) {
            $this->mapElementtype($classMap, $elementtype);

        }

        $classes = array_reverse($classMap->all());
        $output->writeln(count($classes) . ' classes');

        foreach ($classes as $class => $values) {
            $output->writeln("<fg=yellow>$class</fg=yellow>");
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                foreach ($values as $name => $type) {
                    $output->writeln("  <info>$type</info> $name");
                }
            }
        }
        //$output->writeln(print_r($data, 1));

        return 0;
    }

    private function mapElementtype(ClassMap $classMap, Elementtype $elementtype)
    {
        $elementtypeStructure = $elementtype->getStructure();

        if (!$elementtypeStructure) {
            return;
        }

        $rootNode = $elementtypeStructure->getRootNode();

        if (!$rootNode) {
            return;
        }

        $rootData = $this->iterateStructure($classMap, $elementtypeStructure, $rootNode);

        $classMap->add($this->toCamelCase($elementtype->getUniqueId()), $rootData);
    }

    /**
     * @param ClassMap                 $classMap
     * @param ElementtypeStructure     $structure
     * @param ElementtypeStructureNode $node
     *
     * @return array
     */
    private function iterateStructure(ClassMap $classMap, ElementtypeStructure $structure, ElementtypeStructureNode $node)
    {
        $fieldRegistry = $this->getContainer()->get('phlexible_elementtype.field.registry');
        $data = array();

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $fieldRegistry->getField($childNode->getType());
            if ($field->isField()) {
                $data[$childNode->getName()] = $childNode->getType();
            } else {
                if ($childNode->isOptional() || $childNode->isRepeatable()) {
                    $data[$childNode->getName()] = $this->toCamelCase($childNode->getName()) . '[]';
                    $map = $this->iterateStructure($classMap, $structure, $childNode);
                    $classMap->add($this->toCamelCase($childNode->getName()), $map);
                } else {
                    $data = array_merge($data, $this->iterateStructure($classMap, $structure, $childNode));
                }
            }
        }

        return $data;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function toCamelCase($str)
    {
        // Split string in words.
        $words = preg_split('/[-_]/', strtolower($str)); //explode('_', strtolower($str));

        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }

        return ($return);
    }
}
