<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/http', name: 'http_')]
class HttpRequestController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly HttpClientInterface $jsonplaceholderClient
    ){}

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $users = [];

        try {
            $response = $this->httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users', [
                'auth_bearer' => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
            ]);
            $users = $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->addFlash('danger', "Une erreur est survenue ....");
        }

        return $this->render('http_request/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/jsonplaceholder', name: 'jsonplaceholder')]
    public function jsonplaceholder(): Response
    {
        $users = [];

        try {
            $response = $this->jsonplaceholderClient->request('GET', '/users');
            $users = $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->addFlash('danger', "Une erreur est survenue ....");
        }

        return $this->render('http_request/index.html.twig', [
            'users' => $users
        ]);
    }
}
