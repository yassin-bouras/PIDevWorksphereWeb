<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(string $to, string $subject, string $content): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('worksphere12345@gmail.com', 'WorkSphere'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('Extras/mail.html.twig')
            ->context([
                'subject' => $subject,
                'content' => $content,
            ]);

        $logoPath = __DIR__ . '/../../public/Images/worksphere.png';
        if (file_exists($logoPath)) {
            $email->embedFromPath($logoPath, 'logo');
        }

        $this->mailer->send($email);
    }
}