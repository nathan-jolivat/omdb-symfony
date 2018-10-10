<?php

namespace App\Controller;

use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class MailController extends AbstractController
{
    private $mailer;

    /**
     * MailController constructor. Inject SwiftMailerService into childs methods
     *
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send mail with movie informations to a specific recipient
     *
     * @Route( "/send-mail", name="send-mail")
     *
     * @param              $recipient
     * @param              $movieDetails
     *
     * @return string
     */
    public function sendMail($recipient, array $movieDetails)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                "coucou",
                'text/html'
            )
        ;

        die();
    }
}