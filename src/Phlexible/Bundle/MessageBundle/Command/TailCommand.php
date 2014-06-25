<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Tail command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TailCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('message:tail')
            ->setDescription('Show latest messages')
            ->setDefinition(array(
                new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Show latest <limit> messages.', 20),
                new InputOption('follow', 'f', InputOption::VALUE_NONE, 'Follow output'),
                new InputOption('body', 'b', InputOption::VALUE_NONE, 'Show body'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit  = $input->getOption('limit');
        $follow = $input->getOption('follow');
        $showBody   = $input->getOption('body');

        $messageManager = $this->getContainer()->get('phlexible_message.message_manager');
        $priorities = $messageManager->getPriorityNames();
        $types      = $messageManager->getTypeNames();

        if ($limit) {
            $messages = $messageManager->findBy(array(), array('createdAt' => 'DESC'), $limit);
            $messages = array_reverse($messages);
            foreach ($messages as $message) {
                $output->writeln(
                    sprintf(
                        "[%s] %s.%s: %s [%s, %s]",
                        $message->getCreatedAt()->format('Y-m-d H:i:s'),
                        $priorities[$message->getPriority()],
                        $types[$message->getType()],
                        $message->getSubject(),
                        $message->getChannel() ?: '-',
                        $message->getResource() ?: '-'
                    )
                );
                if ($showBody) {
                    $output->writeln(' > ' . $message->getBody());
                }
            }
        }

        if (!$follow) {
            return 0;
        }

        $message = $messageManager->findOneBy(array(), array('createdAt' => 'ASC'));
        $minTime = $message->getCreatedAt();

        while (1) {
            $criteria = new Criteria();
            $criteria->dateFrom($minTime);

            $messages = $messageManager->findByCriteria($criteria, array('createdAt' => 'ASC'), null, null);
            //$output->writeln($minTime->format('Y-m-d H:i:s').' '.count($messages));

            foreach ($messages as $message) {
                $time = $message->getCreateTime();

                if ($time <= $minTime) {
                    continue;
                }

                $minTime = $time;

                $output->writeln(
                    sprintf(
                        "[%s] %s.%s: %s [%s] [%s]",
                        $message->getCreatedAt()->format('Y-m-d H:i:s'),
                        $priorities[$message->getPriority()],
                        $types[$message->getType()],
                        $message->getSubject(),
                        $message->getChannel() ?: '-',
                        $message->getResource() ?: '-'
                    )
                );

                if ($showBody || $message->getPriority() >= Message::PRIORITY_URGENT || $message->getType() === Message::TYPE_ERROR) {
                    $output->writeln(' > ' . $message->getBody());
                }
            }

            sleep(1);
        }

        return 0;
    }

}
