<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Command;

use Phlexible\Bundle\ProblemBundle\ProblemsMessage;
use Phlexible\Security\Authentication\Token\PreAuthenticatedToken;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Check command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CheckCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('problems:check')
            ->setDescription('Run cached problem checks.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $problemCheckers = $this->getContainer()->get('phlexible_problem.problem_checkers');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $problemsRepository = $em->getRepository('PhlexibleProblemBundle:Problem');

        $problemList = ['add' => [], 'remove' => []];

        foreach ($problemCheckers as $problemChecker) {
            $problems = $problemChecker->check();

            $existingProblems = $problemsRepository->findByCheckClass(get_class($problemChecker));

            $problemIds = [];
            foreach ($problems as $problemKey => $problem) {
                $problemIds[$problem->getId()] = $problemKey;
            }

            foreach ($existingProblems as $existingProblemKey => $existingProblem) {
                $existingProblemId = $existingProblem->getId();

                if (array_key_exists($existingProblemId, $problemIds)) {
                    $output->writeln("= " . $existingProblemId);
                    $existingProblem->setLastCheckedAt(new \DateTime());
                    unset ($problems[$problemIds[$existingProblemId]]);
                    unset ($existingProblems[$existingProblemKey]);
                }
            }

            foreach ($problems as $problem) {
                $problemList['add'][] = $problem->getId();

                $output->writeln("<fg=red>+ {$problem->getId()} </fg=red>");
                $problem->setCreatedAt(new \DateTime());
                $problem->setLastCheckedAt(new \DateTime());
                $em->persist($problem);
            }

            foreach ($existingProblems as $existingProblem) {
                $problemList['remove'][] = $existingProblem->getId();

                $output->writeln("<fg=green>- {$existingProblem->getId()}</fg=green>");
                $em->remove($existingProblem);
            }
        }

        $em->flush();

        $subject = null;
        $total = null;
        $body = 'Changes:';
        $priority = null;
        $countAdd = count($problemList['add']);
        $countRemove = count($problemList['remove']);
        if ($countAdd) {
            $body .= PHP_EOL . '+ ' . implode(PHP_EOL . '+ ', $problemList['add']);
            $subject = "Problem check found $countAdd new problem(s)";
            $total = "Problem check found <fg=red>$countAdd new</fg=red> problem(s)";
            $priority = ProblemsMessage::PRIORITY_HIGH;
        }
        if ($countRemove) {
            $body .= PHP_EOL . '- ' . implode(PHP_EOL . '- ', $problemList['remove']);
            $subject = "Problem check removed $countRemove existing problem(s)";
            $total = "Problem check removed <fg=green>$countRemove existing</fg=green> problem(s)";
            $priority = ProblemsMessage::PRIORITY_NORMAL;
        }
        if ($countAdd && $countRemove) {
            $subject = "Problem check found $countAdd new and removed $countRemove existing problem(s)";
            $total = "Problem check found <fg=red>$countAdd new</fg=red> and removed <fg=green>$countRemove existing</fg=green> problem(s)";
            $priority = ProblemsMessage::PRIORITY_HIGH;
        }

        if (isset($subject)) {
            $this->getContainer()->get('phlexible_message.message_poster')
                ->post(ProblemsMessage::create($subject, $body, $priority));
        }

        if ($total) {
            $output->writeln($total);
        }

        $properties = $this->getContainer()->get('properties');
        $properties->set('problems', 'last_run', date('Y-m-d H:i:s'));

        return 0;
    }

}
