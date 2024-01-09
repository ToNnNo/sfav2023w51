<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Turbo\TurboBundle;

class TurboController extends AbstractController
{
    #[Route('/turbo', name: 'turbo_index')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add("message", TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add("send", SubmitType::class, [
                'label' => "Envoyer",
                'attr' => [
                    'class' => 'btn-outline-primary'
                ]
            ])
            ->getForm();
        $emptyForm = clone $form;
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->addFlash('success', $data['message']);

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('turbo/success.stream.html.twig', [
                    'form' => $emptyForm
                ]);
            }

            return $this->redirectToRoute('turbo_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('turbo/index.html.twig', [
            'form' => $form
        ]);
    }
}
