<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Mailer;

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
    public function __construct(
        Twig_Environment $templating,
        Swift_Mailer $mailer,
        array $parameters)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    /**
     * Send an email to a user showing the new password
     *
     * @param UserInterface $user
     */
    public function sendNewAccountEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['new_account']['template'];
        $from = $this->parameters['new_account']['from'];

        $content = $this->templating->render(
            $template,
            array(
                'user' => $user,
            )
        );
        $this->sendEmailMessage($content, $from, $user->getEmail());
    }

    /**
     * Send an email to a user showing the new password
     *
     * @param UserInterface $user
     */
    public function sendNewPasswordEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['new_password']['template'];
        $from = $this->parameters['new_password']['from'];

        $content = $this->templating->render(
            $template,
            array(
                'user' => $user,
            )
        );
        $this->sendEmailMessage($content, $from, $user->getEmail());
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
