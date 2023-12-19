<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Service\ContactEmail;
use App\Service\MessageGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service', name: 'service_')]
class ServiceController extends AbstractController
{

    public function __construct(
        private readonly MessageGenerator $messageGenerator,
        private readonly ContactEmail     $contactEmail,
        private readonly LoggerInterface  $logger
    )
    {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        // FQCN = namespace + nom de la classe
        // ServiceController::class; // App\Controller\ServiceController

        $message = $this->messageGenerator->getHappyMessage();

        return $this->render('service/index.html.twig', [
            'message' => $message
        ]);
    }

    #[Route('/contact', name: 'contact_form')]
    public function contact(Request $request): Response
    {
        /**
         * php bin/console make:form
         *
         * Form/ContactType (ne doit pas être rattaché à une entité)
         *
         * nom: TextType
         * email: EmailType
         * sujet: ChoiceType
         * message: TextareaType
         *
         * le form s'affiche avec le style de bootstrap (config/package/twig.yaml)
         *
         * Créer le formulaire dans le controller + récupération des valeurs
         */

        $initialData = [
            'name' => "John Doe",
            'email' => "john.doe@gmail.com",
            'message' => "Hello World"
        ];
        $form = $this->createForm(ContactFormType::class, $initialData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->contactEmail->send($data);
                $this->addFlash('success', "Votre demande a bien été transmise, nous la traiterons dès que possible");
            } catch(TransportExceptionInterface $e) {
                $this->addFlash('warning', "Un problème est survenu lors de l'envoie du message");
                $this->logger->warning($e->getMessage());
            }

            return $this->redirectToRoute('service_contact');
        }

        return $this->render('service/contact.html.twig', [
            'form' => $form
        ]);
    }
}
