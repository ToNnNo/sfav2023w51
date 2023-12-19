<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact', name: 'contact_', requirements: ['id' => "\d+"])]
class ContactController extends AbstractController
{

    public function __construct(
        private readonly ContactRepository      $userRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $contacts = $this->userRepository->findAll();

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->addFlash('success', "Le contact a bien été créé");

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Contact $contact, Request $request): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', "Le contact a bien été modifié");

            return $this->redirectToRoute('contact_edit', ['id' => $contact->getId()]);
        }

        return $this->render('contact/edit.html.twig', [
            'form' => $form,
            'contact' => $contact
        ]);
    }

    #[Route('/{id}/delete/{token}', name: 'delete')]
    public function delete(Contact $contact, string $token): Response
    {
        $tokenId = "delete-contact-".$contact->getId();

        if ($this->isCsrfTokenValid($tokenId, $token)) {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();

            $this->addFlash('success', "Le contact a bien été supprimé");
        }

        return $this->redirectToRoute('contact_index');
    }
}
