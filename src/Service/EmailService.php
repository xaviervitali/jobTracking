<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{

    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendHtmlEmail(String $to, String $subject, String $content)
    {
        $email = (new Email())
            ->from('no-reply@job-tracking.free.nf')  // Adresse d'expéditeur personnalisée
            ->to($to)                             // Destinataire
            ->subject($subject)                   // Sujet
            ->html($content);                     // Contenu de l'e-mail

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Gérer l'exception si nécessaire
        }
    }
}
