<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ContactEmail
{

    public function __construct(
        private MailerInterface     $mailer,
        private TranslatorInterface $translator,
        private string              $contactAddress
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(array $data): void
    {
        $data['mail'] = $data['email'];
        unset($data['email']);

        if (!empty($data['name'])) {
            $address = new Address($data['mail'], $data['name']);
        } else {
            $address = $data['mail'];
        }

        // $contactAddress = 'smenut@dawan.fr';

        $email = (new TemplatedEmail())
            ->from($address)
            ->to($this->contactAddress)
            ->replyTo($data['mail'])
            ->subject($this->translator->trans($data['subject']))
            ->textTemplate('email/contact.txt.twig')
            ->context($data);

        $this->mailer->send($email);
    }

}
