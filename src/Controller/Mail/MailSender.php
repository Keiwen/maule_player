<?php

namespace App\Controller\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailSender
{

    protected $mailer;
    protected $appSender;
    protected $appName;
    protected $lastException;

    public function __construct(MailerInterface $mailer, ContainerBagInterface $appParameters)
    {
        $this->mailer = $mailer;
        $this->appSender = $appParameters->get('email_sender');
        $this->appName = $appParameters->get('app_name');
    }

    /**
     * @param string|array $to
     * @param string $subject
     * @param string $template refer to template name, relative to "email/{templateName}.html.twig
     * @param array $context parameter sent to template
     * @return bool
     */
    public function sendMail($to, string $subject, string $template, array $context = []): bool
    {
        $email = (new TemplatedEmail())
            ->from($this->appSender)
            ->replyTo($this->appSender)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->appName . ' - ' . $subject)
            ->htmlTemplate('email/' . $template . '.html.twig')
            // pass variables (name => value) to the template
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->lastException = $e;
            return false;
        }
        return true;
    }


    public function getLastException(): TransportExceptionInterface
    {
        return $this->lastException;
    }

}
