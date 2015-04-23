<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Command;

use Phlexible\Bundle\GuiBundle\Requirement\PhlexibleRequirements;
use Phlexible\Bundle\GuiBundle\Requirement\Requirement;
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
            ->setName('phlexible:check')
            ->setDescription('Check phlexible requirements');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phlexibleRequirements = new PhlexibleRequirements($this->getContainer());
        $lineSize = 70;
        $formatter = $this->getHelper('formatter');

        $formattedBlock = $formatter->formatBlock(array('phlexible Requirements Checker', '~~~~~~~~~~~~~~~~~~~~~~~~~~~'), 'fg=blue', true);
        $output->writeln($formattedBlock);

        $output->write('> Checking phlexible requirements:'.PHP_EOL.'  ');


        $messages = array();
        foreach ($phlexibleRequirements->getRequirements() as $req) {
            /** @var $req Requirement */
            if ($helpText = $this->getErrorMessage($req, $lineSize)) {
                $output->write('<fg=red>E</fg=red>');
                $messages['error'][] = $helpText;
            } else {
                $output->write('<fg=green>.</fg=green>');
            }
        }

        $checkPassed = empty($messages['error']);

        foreach ($phlexibleRequirements->getRecommendations() as $req) {
            if ($helpText = $this->getErrorMessage($req, $lineSize)) {
                $output->write('<fg=yellow>W</fg=yellow>');
                $messages['warning'][] = $helpText;
            } else {
                $output->write('<fg=green>.</fg=green>');
            }
        }

        $output->writeln('');
        $output->writeln('');

        if ($checkPassed) {
            $formattedBlock = $formatter->formatBlock(array('[OK]', 'Your system is ready to run phlexible projects'), 'bg=green', true);
            $output->writeln($formattedBlock);
        } else {
            $formattedBlock = $formatter->formatBlock(array('[ERROR]', 'Your system is not ready to run phlexible projects'), 'bg=red', true);
            $output->writeln($formattedBlock);

            $output->writeln('');

            $output->writeln('<fg=red>Fix the following mandatory requirements</fg=red>');

            foreach ($messages['error'] as $helpText) {
                $output->writeln(' * '.$helpText);
            }
        }

        if (!empty($messages['warning'])) {
            $output->writeln('');

            $output->writeln('<fg=yellow>Optional recommendations to improve your setup</fg=yellow>');

            foreach ($messages['warning'] as $helpText) {
                $output->writeln(' * '.$helpText);
            }
        }

        return $checkPassed ? 0 : 1;
    }

    private function getErrorMessage(Requirement $requirement, $lineSize)
    {
        if ($requirement->isFulfilled()) {
            return;
        }

        $errorMessage  = wordwrap($requirement->getTestMessage(), $lineSize - 3, PHP_EOL.'   ').PHP_EOL;
        $errorMessage .= '   > '.wordwrap($requirement->getHelpText(), $lineSize - 5, PHP_EOL.'   > ').PHP_EOL;

        return $errorMessage;
    }

}
