<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Mailer;

use Phlexible\Bundle\TaskBundle\Entity\Comment;
use Phlexible\Bundle\TaskBundle\Entity\Status;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Entity\Transition;
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
     * Send a new task email
     *
     * @param Task          $task
     * @param UserInterface $createUser
     * @param UserInterface $assignUser
     * @param TypeInterface $type
     */
    public function sendNewTaskEmailMessage(
        Task $task,
        UserInterface $createUser,
        UserInterface $assignUser,
        TypeInterface $type)
    {
        // $createUser, $recipientUser, $task, $link
        $template = $this->parameters['new_task']['template'];
        $from = $this->parameters['new_task']['from_email'];

        $text = $type->getText($task);
        $url = $type->getLink($task);

        $content = $this->templating->render(
            $template,
            array(
                'createUser' => $createUser,
                'assignUser' => $assignUser,
                'task'       => $task,
                'text'       => $text,
                'url'        => $url
            )
        );
        $this->sendEmailMessage($content, $from, $assignUser->getEmail());
    }

    /**
     * Send an update email
     *
     * @param Task            $task
     * @param UserInterface   $byUser
     * @param UserInterface[] $involvedUsers
     * @param array           $changes
     * @param TypeInterface   $type
     */
    public function sendUpdateEmailMessage(Task $task, UserInterface $byUser, array $involvedUsers, array $changes, TypeInterface $type)
    {
        $template = $this->parameters['update']['template'];
        $from = $this->parameters['update']['from_email'];

        $text = $type->getText($task);
        $url = $type->getLink($task);

        $content = $this->templating->render(
            $template,
            array(
                'byUser'        => $byUser,
                'involvedUsers' => $involvedUsers,
                'changes'       => $changes,
                'text'          => $text,
                'url'           => $url
            )
        );

        foreach ($involvedUsers as $involvedUser) {
            $this->sendEmailMessage($content, $from, $involvedUser->getEmail());
        }
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
