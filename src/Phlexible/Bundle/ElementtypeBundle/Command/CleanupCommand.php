<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDefinition(
                [
                    new InputOption('delete', null, InputOption::VALUE_NONE, 'Really delete'),
                ]
            )
            ->setDescription('Cleanup old/unused elementtypes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reallyDelete = $input->getOption('delete');

        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');
        /* @var $conn Connection */

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(etv.id)')
            ->from('elementtype_version', 'etv');

        $etVersionCnt = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select('DISTINCT et.id')
            ->from('elementtype', 'et')
            ->join('et', 'element', 'e', 'e.elementtype_id = et.id')
            ->orderBy('et.id', 'ASC');

        $etIds = array_column($conn->fetchAll($qb->getSQL()), 'id');

        $qb = $conn->createQueryBuilder()
            ->select('DISTINCT etv.version')
            ->from('elementtype_version', 'etv')
            ->where('elementtype_id = ?')
            ->orderBy('etv.version', 'ASC');

        $deleteUpToETVersion = [];

        foreach ($etIds as $etId) {
            $versions = array_column($conn->fetchAll($qb->getSQL(), [$etId]), 'version');

            foreach ($versions as $version) {
                if ($this->isUsedInElement($conn, $etId, $version)) {
                    continue 2;
                }

                $deleteUpToETVersion[$etId] = $version;
            }
        }

        /*
        $output->writeln(print_r($deleteUpToETVersion, true));
        $output->writeln('---------');
        */

        if (count($deleteUpToETVersion)) {
            $cnt = 1;//$this->deleteVersions($conn, $deleteUpToETVersion, $reallyDelete);
            if ($reallyDelete) {
                $output->write('Deleted ');
            } else {
                $output->write('Would delete ');
            }
            $output->writeln($cnt . ' old elementtype versions (of ' . $etVersionCnt . ')');
        } else {
            $output->writeln('No old elementtype versions.');
        }

        return 0;
    }

    /**
     * @param Connection $conn
     * @param array      $versions
     * @param bool       $reallyDelete
     *
     * @return int
     */
    private function deleteVersions(Connection $conn, array $versions, $reallyDelete = false)
    {
        $conn->beginTransaction();

        $total = 0;
        foreach ($versions as $etId => $version) {
            $qb = $conn->createQueryBuilder()
                ->delete()
                ->from('elementtype_version', 'etv')
                ->where('etv.elementtype_id = ?')
                ->andWhere('etv.version < ?');

            $total += $conn->executeUpdate($qb->getSQL(), [$etId, $version]);

            //echo $eid . ' => '.$cnt.PHP_EOL;
        }

        if ($reallyDelete) {
            $conn->commit();
        } else {
            $conn->rollback();
        }

        return $total;
    }

    /**
     * @param Connection $conn
     * @param int        $etId
     * @param int        $version
     *
     * @return bool
     */
    private function isUsedInElement(Connection $conn, $etId, $version)
    {
        $select = $conn->createQueryBuilder()
            ->select('e.eid')
            ->from('element_version', 'ev')
            ->join('ev', 'element', 'e', 'ev.eid = e.eid')
            ->where('e.elementtype_id = ?')
            ->andWhere('ev.version = ?');

        return (bool) $conn->fetchColumn($select, [$etId, $version]);
    }
}
