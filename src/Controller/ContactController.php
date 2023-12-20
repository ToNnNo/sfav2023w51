<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact', name: 'contact_', requirements: ['id' => "\d+"])]
class ContactController extends AbstractController
{

    public const PLACEHOLDER = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22250%22%20height%3D%22250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20250%20250%22%20preserveAspectRatio%3D%22none%22%3E%0A%20%20%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3Cstyle%20type%3D%22text%2Fcss%22%3E%0A%20%20%20%20%20%20%20%20%20%20%23holder%20text%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20%23ffffff%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20font-family%3A%20sans-serif%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20font-size%3A%2040px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20font-weight%3A%20400%3B%0A%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%3C%2Fstyle%3E%0A%20%20%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%20%20%3Cg%20id%3D%22holder%22%3E%0A%20%20%20%20%20%20%20%20%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23cccccc%22%3E%3C%2Frect%3E%0A%20%20%20%20%20%20%20%20%3Cg%3E%0A%20%20%20%20%20%20%20%20%20%20%3Ctext%20text-anchor%3D%22middle%22%20x%3D%2250%25%22%20y%3D%2250%25%22%20dy%3D%22.3em%22%3E250%20x%20250%3C%2Ftext%3E%0A%20%20%20%20%20%20%20%20%3C%2Fg%3E%0A%20%20%20%20%20%20%3C%2Fg%3E%0A%20%20%20%20%3C%2Fsvg%3E";

    public function __construct(
        private readonly ContactRepository      $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager
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

            $picture = $form->get('picture')->getData();

            if ($picture != null) {
                $filename = $this->fileManager->upload($picture);
                $contact->setPicture($filename);
            }

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

            $picture = $form->get('picture')->getData();

            if ($picture != null) {
                $filename = $this->fileManager->upload($picture);
                $this->fileManager->remove($contact->getPicture());
                $contact->setPicture($filename);
            }

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
        $tokenId = "delete-contact-" . $contact->getId();

        if ($this->isCsrfTokenValid($tokenId, $token)) {
            $this->fileManager->remove($contact->getPicture());
            $this->entityManager->remove($contact);
            $this->entityManager->flush();

            $this->addFlash('success', "Le contact a bien été supprimé");
        }

        return $this->redirectToRoute('contact_index');
    }
}
