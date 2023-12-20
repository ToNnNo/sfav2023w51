<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contacts', name: 'api_contact_', requirements: ['id' => '\d+'])]
class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository      $contactRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[Route('', name: 'index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $sort_field = $request->query->get('sort_field');
        $sort_direction = $request->query->get('sort_direction', 'ASC');

        $filter_field = $request->query->get('filter_field');
        $filter_value = $request->query->get('filter_value');

        $sort = [];
        if ($sort_field != null) {
            $sort = [$sort_field => $sort_direction];
        }

        $filter = [];
        if ($filter_field != null && $filter_value != null) {
            $filter = [$filter_field => $filter_value];
        }

        $contacts = $this->contactRepository->findBy($filter, $sort);

        return $this->json($contacts, context: ['groups' => 'contact_read_list']);
    }

    #[Route('/{id}', name: 'detail', methods: 'GET')]
    public function detail(int $id): Response
    {
        $contact = $this->contactRepository->find($id);

        if (!$contact) {
            return $this->json(['status' => Response::HTTP_NOT_FOUND, 'errors' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($contact, context: ['groups' => 'contact_read_detail']);
    }

    #[Route('', name: 'create', methods: 'POST')]
    public function create(Request $request): Response
    {
        $contact = $this->serializer->deserialize($request->getContent(), Contact::class, 'json');

        $errors = $this->validator->validate($contact);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $errors
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $this->json($contact, Response::HTTP_CREATED, context: ['groups' => 'contact_read_detail']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): Response
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            return $this->json(['status' => Response::HTTP_NOT_FOUND, 'errors' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        $this->serializer->deserialize($request->getContent(), Contact::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $contact
        ]);

        $errors = $this->validator->validate($contact);
        if ($errors->count() > 0) {
            return $this->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $errors
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        return $this->json($contact, Response::HTTP_OK, context: ['groups' => 'contact_read_detail']);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            return $this->json(['status' => Response::HTTP_NOT_FOUND, 'errors' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
