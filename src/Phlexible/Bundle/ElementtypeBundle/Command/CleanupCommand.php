<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cleanup command
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class CleanupCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elementtypes:cleanup')
            ->setDefinition(array(
                new InputOption('delete', null, InputOption::VALUE_NONE, 'Really delete'),
            ))
            ->setDescription('Cleanup old/unused elementtypes.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reallyDelete = $input->getOption('delete');

        $container = $this->getContainer();

        $db = $container->dbPool->default;

        $etVersionsCntSelect = $db->select()
            ->from($db->prefix . 'elementtype_version', new Zend_Db_Expr('COUNT(*)'));
        $etVersionCnt        = $db->fetchOne($etVersionsCntSelect);


        $etSelect = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'elementtype'), 'element_type_id')
            ->join(array('e' => $db->prefix . 'element'), 'e.element_type_id = et.element_type_id', array())
            ->order('et.element_type_id ASC');

        $etIds = $db->fetchCol($etSelect);

        $etvSelect = $db->select()
            ->distinct()
            ->from($db->prefix . 'elementtype_version', 'version')
            ->where('element_type_id = ?')
            ->order('version ASC');

        $deleteUpToETVersion = array();

        foreach ($etIds as $etId)
        {
            $versions = $db->fetchCol($etvSelect, $etId);

            foreach ($versions as $version)
            {
                if ($this->_isUsedInElement($etId, $version))
                {

                    continue 2;
                }

                $deleteUpToETVersion[$etId] = $version;
            }
        }

        /*
        $output .= print_r($deleteUpToETVersion, true);
        $output .= '---------'.PHP_EOL;
        */

        if (count($deleteUpToETVersion))
        {
            $cnt = $this->_deleteVersions($deleteUpToETVersion, $reallyDelete);
            if ($reallyDelete)
            {
                $output->write('Deleted ');
            }
            else
            {
                $output->write('Would delete ');
            }
            $output->writeln($cnt . ' old elementtype versions (of '.$etVersionCnt.')');
        }
        else
        {
            $output->writeln('No old elementtype versions.');
        }

        return 0;
    }

    protected function _deleteVersions(array $versions, $reallyDelete)
    {
        $db = MWF_Registry::getContainer()->dbPool->default;
        $db->beginTransaction();

        $total = 0;
        foreach ($versions as $etId => $version)
        {
            $cnt = $db->delete(
                $db->prefix . 'elementtype_version',
                array(
                    'element_type_id = ?' => $etId,
                    'version <= ?'        => $version,
                )
            );
            $total += $cnt;

            //echo $eid . ' => '.$cnt.PHP_EOL;
        }

        if ($reallyDelete)
        {
            $db->commit();
        }
        else
        {
            $db->rollback();
        }

        return $total;
    }

    protected function _isUsedInElement($etId, $version)
    {
        $db = $this->getContainer()->dbPool->default;

        $select = $db->select()
            ->from($db->prefix . 'element_version', 'eid')
            ->where('element_type_id = ?', $etId)
            ->where('element_type_version = ?', $version);

        $id = $db->fetchOne($select);

        return (boolean)$id;
    }
}
