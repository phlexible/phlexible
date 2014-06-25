<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Command;

use Phlexible\Bundle\ProblemBundle\ProblemsEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\ProblemBundle\Problem;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('problems:list')
            ->setDescription('List problems.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $problemFetcher = $this->getContainer()->get('phlexible_problem.problem_fetcher');

        $problems = $problemFetcher->fetch();
        if (!count($problems)) {
            $output->writeln('<info>No problems found.</info>');

            return 0;
        }

        $cnt = count($problems);
        $output->writeln('<error>Found ' . $cnt . ' Problem' . ($cnt > 1 ? 's' : '') . '</error>');
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(array('Type', 'Severity', 'Problem', 'Solve'));

        foreach ($problems as $problem) {
            /*
            switch ($problem->getSeverity())
            {
                case Problem::SEVERITY_CRITICAL:
                    $severity = '<error>' . $problem->getSeverity() . '</error>';
                    break;

                case Problem::SEVERITY_WARNING:
                    $severity = '<comment>' . $problem->getSeverity() . '</comment>';
                    break;

                default:
                    $severity = '<info>' . $problem->getSeverity() . '</info>';
            }
            */

            $table->addRow(
                array(
                    $problem->isLive() ? 'live' : 'cached',
                    $problem->getSeverity(),
                    $problem->getMessage(),
                    $problem->getHint()
                )
            );
        }

        $table->render($output);

        return 0;
    }

}
