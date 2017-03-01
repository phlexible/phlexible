<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Mailer;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig_Environment;

/**
 * DigestMail Mailer.
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
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param \Twig_Environment $templating
     * @param \Swift_Mailer     $mailer
     * @param LoggerInterface   $logger
     * @param array             $parameters
     */
    public function __construct(
        Twig_Environment $templating,
        \Swift_Mailer $mailer,
        LoggerInterface $logger,
        array $parameters)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->parameters = $parameters;
    }

    /**
     * Send digest mail.
     *
     * @param UserInterface $user
     * @param Message[]     $messages
     *
     * @return bool
     */
    public function sendDigestMail(UserInterface $user, array $messages)
    {
        $template = $this->parameters['digest']['template'];
        $from = $this->parameters['digest']['from'];

        $content = $this->templating->render(
            $template,
            [
                'date' => date('Y-m-d H:i:s'),
                'messages' => $messages,
            ]
        );

        return $this->sendEmailMessage($content, $from, $user->getEmail());
    }

    /**
     * @param string $content
     * @param string $from
     * @param string $recipient
     *
     * @return bool
     */
    private function sendEmailMessage($content, $from, $recipient)
    {
        $lines = explode(PHP_EOL, trim($content));
        $subject = $lines[0];
        $body = implode(PHP_EOL, array_slice($lines, 1));

        try {
            $mail = new \Swift_Message();
            $mail
                ->setSubject($subject)
                ->setBody($body)
                ->setFrom($from)
                ->addTo($recipient);

            $this->mailer->send($mail);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__.' failed to send mail: '.$e->getMessage());

            return false;
        }

        return true;
    }
}
