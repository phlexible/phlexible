<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Mailer;

use Phlexible\Bundle\TaskBundle\Entity\Status;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig_Environment;

/**
 * User mailer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Mailer
{
    /**
     * @var Twig_Environment
     */
    private $templating;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param Twig_Environment $templating
     * @param Swift_Mailer     $mailer
     * @param array            $parameters
     */
    public function __construct(Twig_Environment $templating, Swift_Mailer $mailer, array $parameters)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    /**
     * Send an email to a user with the new task
     *
     * @param UserInterface $createUser
     * @param UserInterface $recipientUser
     * @param Status        $taskStatus
     * @param TypeInterface $type
     */
    public function sendNewTaskEmailMessage(
        UserInterface $createUser,
        UserInterface $recipientUser,
        Status $taskStatus,
        TypeInterface $type)
    {
        // $createUser, $recipientUser, $task, $link
        $template = $this->parameters['new_task']['template'];
        $from = $this->parameters['new_task']['from'];

        $task = $taskStatus->getTask();
        $text = $type->getText($task);
        $url = $type->getLink($task);

        $content = $this->templating->render(
            $template,
            array(
                'user'       => $createUser,
                'fromUser'   => $recipientUser,
                'taskStatus' => $taskStatus,
                'text'       => $text,
                'url'        => $url
            )
        );
        $this->sendEmailMessage($content, $from, $recipientUser->getEmail());
    }

    /**
     * Send an email to a user with the new status
     *
     * @param UserInterface $createUser
     * @param UserInterface $recipientUser
     * @param Status        $taskStatus
     * @param TypeInterface $type
     */
    public function sendNewStatusEmailMessage(
        UserInterface $createUser,
        UserInterface $recipientUser,
        Status $taskStatus,
        TypeInterface $type)
    {
        $template = $this->parameters['new_status']['template'];
        $from = $this->parameters['new_status']['from'];

        $task = $taskStatus->getTask();
        $text = $type->getText($task);
        $url = $type->getLink($task);

        $content = $this->templating->render(
            $template,
            array(
                'user'       => $createUser,
                'fromUser'   => $recipientUser,
                'taskStatus' => $taskStatus,
                'text'       => $text,
                'url'        => $url
            )
        );
        $this->sendEmailMessage($content, $from, $recipientUser->getEmail());
    }

    /**
     * @param string $content
     * @param string $from
     * @param string $email
     */
    private function sendEmailMessage($content, $from, $email)
    {
        $lines = explode(PHP_EOL, trim($content));
        $subject = $lines[0];
        $body = implode(PHP_EOL, array_slice($lines, 1));

        $mail = Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($email)
            ->setSubject($subject)
            ->setBody($body);

        $this->mailer->send($mail);
    }
}