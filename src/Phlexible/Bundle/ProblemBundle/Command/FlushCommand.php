<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flush command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FlushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('problems:flush')
            ->setDescription('Flush cached problems.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $problemsRepository = $em->getRepository('PhlexibleProblemBundle:Problem');
        $problems = $problemsRepository->findAll();

        foreach ($problems as $problem) {
            $em->remove($problem);
        }

        $em->flush();

        $output->writeln(count($problems) . ' problems flushed.');

        return 0;
    }

}
