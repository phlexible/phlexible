<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:delete')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Username / User ID to delete'),
                new InputArgument('successor', null, InputArgument::REQUIRED, 'Username / User ID to set as successor'),
            ))
            ->setDescription('Delete user')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username  = $input->getArgument('username');
        $successorUsername = $input->getArgument('successor');

        $uid = $this->findUser($username);
        $successorUid = $this->findUser($successorUsername);

        $output->writeln("Using delete UID $uid");
        $output->writeln("Using successor UID $successorUid");

        \MWF_Core_Users_User_Peer::deleteByUserID($uid, $successorUid);

        $output->writeln('User deleted.');

        return 0;
    }

    private function findUser($username)
    {
        $validator      = new \Brainbits_Validate_Uuid();
        $usernameIsUid  = $validator->isValid($username);

        if ($usernameIsUid)
        {
            return $username;
        }

        $db = $this->getContainer()->dbPool->default;

        $select = $db->select()
            ->from($db->prefix . 'user', 'uid')
            ->where('username = ?', $username);
        $uid = $db->fetchOne($select);

        if (!$uid)
        {
            throw new \Exception('User '.$username.' not found.');
        }

        return $uid;
    }
}
