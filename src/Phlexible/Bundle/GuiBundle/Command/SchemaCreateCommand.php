<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Schema create command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SchemaCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('schema:update')
            ->setDescription('Update database schema.')
            ->addOption('execute', null, InputOption::VALUE_NONE, 'Execute sql.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->getContainer()->get('doctrine.dbal.default_connection');

        $schemaManager = $conn->getSchemaManager();
        $fromSchema = $schemaManager->createSchema();

        $toSchema = new Schema();

        foreach ($this->getContainer()->getParameter('kernel.bundles') as $class) {
            $reflection = new \ReflectionClass($class);
            $classname = $reflection->getNamespaceName() . '\Schema\Install';
            if (class_exists($classname) && method_exists($classname, 'install')) {
                $install = new $classname();
                $install->install($toSchema);
            }
        }

        $sqls = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());

        if ($input->getOption('execute')) {
            foreach ($sqls as $sql) {
                $conn->exec($sql);
            }
            $output->writeln(count($sqls) . ' queries executed.');
        } else {
            $sql = implode(';' . PHP_EOL, $sqls);
            $output->writeln($sql . ';');
        }

        return 0;
    }

}
