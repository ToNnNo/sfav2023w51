<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{

    private array $messages = [
        "Bonjour, comment allez vous aujourd'hui ?",
        "Il est vraiment très beau en ce moment !",
        "Bientôt les fêtes de noël, c'est toujours un moment magique",
        "Le gras c'est la vie !"
    ];

    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function getHappyMessage(): string
    {
        $index = array_rand($this->messages);
        $message = $this->messages[$index];
        $this->logger->info(
            sprintf("Message sélectionné: %s", $message)
        );

        return $message;
    }
}
