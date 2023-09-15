<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendMail($from, $to, $subject, $template, $context = []): void
    {
        $mail = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        try {
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {

        }
    }

    public function sendRegistrationEmail(User $user): void
    {
        $this->sendMail(
            'hello@snowtricks.com',
            $user->getEmail(),
            'Snowtricks : Account Validation',
            'mail/registration.html.twig',
            [
                'username' => $user->getUsername(),
                'confirmUrl' => $this->urlGenerator->generate(
                    'app_validation',
                    ['token' => $user->getRegistrationToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );
    }

    public function sendResetPasswordEmail(User $user): void
    {
        $this->sendMail(
            'hello@snowtricks.com',
            $user->getEmail(),
            'Snowtricks : Reset password',
            'mail/forgot_password.html.twig',
            [
                'username' => $user->getUsername(),
                'resetPasswordUrl' => ''
            ]
        );
    }
}
