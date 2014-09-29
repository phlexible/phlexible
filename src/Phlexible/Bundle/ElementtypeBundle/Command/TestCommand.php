<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
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

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('et.*')
            ->from('elementtype', 'et')
        ;

        $filesystem = new Filesystem();
        $path = $this->getContainer()->getParameter('kernel.root_dir') . '/Resources/elementtypes/';
        $filesystem->mkdir($path);

        foreach ($conn->fetchAll($qb->getSQL()) as $row) {
            $versionQb = $conn->createQueryBuilder();
            $versionQb
                ->select('etv.*')
                ->from('elementtype_version', 'etv')
                ->where("elementtype_id = {$row['id']}")
                ->orderBy('etv.version', 'DESC');
            $versionRow = $conn->fetchAssoc($versionQb->getSQL());

            $structure = $this->buildStructure($conn, $row['id'], $versionRow['version']);

            $elementtype = new Elementtype();
            $elementtype
                ->setId($row['unique_id'])
                ->setName($row['title'])
                ->setType($row['type'])
                ->setRevision($versionRow['version'])
                ->setIcon($row['icon'])
                ->setDefaultTab($row['default_tab'])
                ->setHideChildren($row['hide_children'])
                ->setDeleted($row['deleted'])
                ->setComment($versionRow['comment'])
                ->setDefaultContentTab($versionRow['default_content_tab'])
                ->setMetaSetId($versionRow['metaset_id'])
                ->setMappings(json_decode($versionRow['mappings'], true))
                ->setStructure($structure)
                ->setCreatedAt(new \DateTime($row['created_at']))
                ->setCreateUserId($row['create_user_id'])
                ->setModifiedAt(new \DateTime($versionRow['created_at']))
                ->setModifyUserId($versionRow['create_user_id'])
            ;

            $filesystem->dumpFile($path . $row['unique_id'] . '.xml', $dumper->dump($elementtype));
        }

        return 0;
    }

    private function buildStructure(Connection $conn, $id, $version)
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
            $labels = json_decode($row['labels'], true);
            $options = json_decode($row['options'], true);
            $configuration = json_decode($row['configuration'], true);
            $validation = json_decode($row['validation'], true);

            if ($options && isset($options['source'])) {
                $configuration['source'] = $options['source'];
            }

            if ($options && isset($options['source_function'])) {
                $configuration['source_function'] = $options['source_function'];
            }

            if ($options && isset($options['source_list'])) {
                $options = $options['source_list'];
            } else {
                $options = null;
            }

            $node = new ElementtypeStructureNode();
            $node
                ->setDsId($row['ds_id'])
                ->setParentDsId($row['parent_ds_id'])
                ->setType($row['type'])
                ->setName($row['name'])
                ->setComment($row['comment'])
                ->setLabels($labels)
                ->setOptions($options)
                ->setConfiguration($configuration)
                ->setValidation($validation)
            ;

            if ($row['reference_id']) {
                $referenceElementtypeId = $conn->fetchColumn("SELECT unique_id FROM elementtype WHERE id = {$row['reference_id']}");
                $node->setReferenceElementtypeId($referenceElementtypeId);
            }

            $structure->addNode($node);
        }

        return $structure;
    }
}

