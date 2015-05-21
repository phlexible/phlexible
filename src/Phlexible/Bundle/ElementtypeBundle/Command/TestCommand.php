<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Doctrine\DBAL\Connection;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeStructure;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        /* @var $conn Connection */
        $dumper = $this->getContainer()->get('phlexible_elementtype.file.dumper.xml');
        $elementtypeManager = $this->getContainer()->get('phlexible_elementtype.elementtype_manager');

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('elementtype', 'et');

        $filesystem = new Filesystem();
        $path = $this->getContainer()->getParameter('kernel.root_dir') . '/Resources/elementtypes/';
        $filesystem->mkdir($path);

        $rows = $conn->fetchAll($qb->getSQL());

        $map = [];
        foreach ($rows as $row) {
            $uniqueId = str_replace('_', '-', $row['unique_id']);
            $id = md5($uniqueId);
            $id = substr($id, 0, 8) . '-' . substr($id, 8, 4) . '-' . substr($id, 12, 4) . '-' . substr($id, 16, 4) . '-' . substr($id, 20);
            $map[$row['id']] = ['id' => $id, 'uniqueId' => $uniqueId];
        }

        foreach ($rows as $row) {
            $versionQb = $conn->createQueryBuilder();
            $versionQb
                ->select('etv.*')
                ->from('elementtype_version', 'etv')
                ->where("elementtype_id = {$row['id']}")
                ->orderBy('etv.version', 'DESC');
            $versionRow = $conn->fetchAssoc($versionQb->getSQL());

            $structure = $this->buildStructure($conn, $row['id'], $versionRow['version'], $map);

            $mappings = json_decode($versionRow['mappings'], true);
            if (isset($mappings['backend'])) {
                foreach ($mappings['backend']['fields'] as $index => $mappingField) {
                    $mappings['backend']['fields'][$index]['dsId'] = $mappingField['ds_id'];
                    $mappings['backend']['fields'][$index]['title'] = $mappingField['field'];
                }
            }

            $elementtype = new Elementtype();
            $elementtype
                ->setId($map[$row['id']]['id'])
                ->setUniqueId($map[$row['id']]['uniqueId'])
                ->setTitle('de', $row['title'])
                ->setTitle('en', $row['title'])
                ->setType($row['type'])
                ->setRevision($versionRow['version'])
                ->setIcon($row['icon'])
                ->setDefaultTab($row['default_tab'])
                ->setHideChildren($row['hide_children'])
                ->setDeleted($row['deleted'])
                ->setComment($versionRow['comment'])
                ->setDefaultContentTab($versionRow['default_content_tab'])
                ->setMetaSetId($versionRow['metaset_id'])
                ->setMappings($mappings)
                ->setStructure($structure)
                ->setCreatedAt(new \DateTime($row['created_at']))
                ->setCreateUser($row['create_user'])
                ->setModifiedAt(new \DateTime($versionRow['created_at']))
                ->setModifyUser($versionRow['create_user']);

            $output->writeln($row['id'] . " => " . $elementtype->getId() . " " . $elementtype->getUniqueId());

            $elementtypeManager->validateElementtype($elementtype);

            $filesystem->dumpFile($path . $map[$row['id']]['uniqueId'] . '.xml', $dumper->dump($elementtype));
        }

        return 0;
    }

    private function buildStructure(Connection $conn, $id, $version, $map)
    {
        $structure = new ElementtypeStructure();

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('ets.*')
            ->from('elementtype_structure', 'ets')
            ->where("elementtype_id = $id")
            ->andWhere("elementtype_version = $version")
            ->orderBy('ets.sort', 'ASC');

        foreach ($conn->fetchAll($qb->getSQL()) as $row) {
            $type = $row['type'];

            $labels = json_decode($row['labels'], true);
            $options = json_decode($row['options'], true);
            $configuration = json_decode($row['configuration'], true);
            $validation = json_decode($row['validation'], true);

            if (isset($validation['required'])) {
                $configuration['required'] = $validation['required'];
                unset($validation['required']);
            }

            if ($labels) {
                if (isset($labels['fieldlabel'])) {
                    $labels['fieldLabel'] = $labels['fieldlabel'];
                    unset($labels['fieldlabel']);
                }
                if (isset($labels['context_help'])) {
                    $labels['contextHelp'] = $labels['context_help'];
                    unset($labels['context_help']);
                }
            }
            if ($options) {
                if (isset($options['default_value'])) {
                    $configuration['default_value'] = $options['default_value'];
                }

                if (isset($options['text_de'])) {
                    $configuration['text_de'] = $options['text_de'];
                }

                if (isset($options['text_en'])) {
                    $configuration['text_en'] = $options['text_en'];
                }

                if (isset($options['source'])) {
                    $configuration['select_source'] = $options['source'];
                }

                if (isset($options['source_function'])) {
                    $configuration['select_function'] = $options['source_function'];
                }

                if (isset($options['source_list']) && ($type === 'select' || $type === 'multiselect')) {
                    $configuration['select_list'] = $options['source_list'];
                }
            }

            if ($configuration) {
                foreach ($configuration as $key => $value) {
                    if (substr($key, 0, 4) === 'ext-') {
                        unset($configuration[$key]);
                        continue;
                    }
                    if ($key === 'suggest_source' && $value === 'undefined') {
                        unset($configuration[$key]);
                        continue;
                    }
                    if ($row['type'] === 'group' && ($key === 'synchronized' || $key === 'required')) {
                        unset($configuration[$key]);
                        continue;
                    }
                    if ($row['type'] === 'tab') {
                        unset($configuration[$key]);
                        continue;
                    }
                }
            }

            $node = new ElementtypeStructureNode();
            $node
                ->setDsId($row['ds_id'])
                ->setParentDsId($row['parent_ds_id'])
                ->setType($row['type'])
                ->setName($row['name'])
                ->setComment($row['comment'])
                ->setLabels($labels)
                ->setConfiguration($configuration)
                ->setValidation($validation);

            if ($row['reference_id']) {
                $referenceElementtypeId = $map[$row['reference_id']]['id'];
                $node->setReferenceElementtypeId($referenceElementtypeId);
            }

            $structure->addNode($node);
        }

        return $structure;
    }
}

