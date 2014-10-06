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
            ->setName('element:test')
            ->setDescription('test.');
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $elementService = $this->getContainer()->get('phlexible_element.element_service');

        $fieldMapper = $this->getContainer()->get('phlexible_element.field.mapper');
        $connectionManager = $this->getContainer()->get('connection_manager');
        $db = $connectionManager->default;
        $contentLoader = $this->getContainer()->get('phlexible_element.content.loader');

        foreach ($db->fetchAll($db->select()->from('element_online')) as $row) {
            $contentElement = $contentLoader->load($row['eid'], $row['version'], $row['language']);
            print_r($contentElement);
        }
        die;
        $loader = $this->getContainer()->get('phlexible_element.content.loader.xml');
        $loadedContentElement = $loader->load($filename);
        die;


        $language = 'de';

        foreach ($elementRepository->findBy() as $element) {
            foreach ($elementService->getVersions($element) as $version) {
                $elementVersion = $elementService->findElementVersion($element, $version);
                try {
                    $map = $fieldMapper->map($elementVersion, $language);
                    $output->writeln($element->getEid() . ' ' . $version . ': ' . json_encode($map));

                    $fields = array();
                    foreach ($map as $field => $value) {
                        $fields[$language][$field] = $value;
                    }

                    $db->update(
                        $db->prefix . 'element_version',
                        array(
                           'mapped_fields' => json_encode($fields)
                        ),
                        array(
                            'eid = ?'     => $element->getEid(),
                            'version = ?' => $elementVersion->getVersion(),
                        )
                    );
                } catch (\Exception $e) {
                    $output->writeln('<error>' . $element->getEid() . ' ' . $version . '</error>: ' . $e->getMessage());
                }
            }
        }

        die;

        $elementStructureLoader = $this->getContainer()->get('phlexible_element.structure.loader');
        $structure = $elementStructureLoader->load($elementVersion, 'de');

        $rii2 = new \RecursiveIteratorIterator($structure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii2 as $node) {
            echo 'Node ' . $node->getId()." ".$node->getName().PHP_EOL;
            print_r($node->getValues());
        }


        ldd($structure);
        $rii = new \RecursiveIteratorIterator($structure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            echo 'x';//.$node->getId();
        }
        ldd($structure);


        $element = $elementService->findElement(50);
        $elementVersion = $elementService->findElementVersion($element, 1);
        $elementVersionData = $elementService->findElementVersionData($elementVersion, 'de', 13, 12);

        print_r($elementVersionData->getTree());

        return 0;
    }
}

