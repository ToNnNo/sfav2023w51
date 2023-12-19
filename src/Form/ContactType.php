<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('name', options: [
                    'label' => 'form.contact.name.label',
                ])
                ->add('email', EmailType::class, [
                    'label' => 'form.contact.email.label',
                    'constraints' => [new NotBlank(), new Email()]
                ])
                ->add('subject', ChoiceType::class, [
                    'label' => 'form.contact.subject.label',
                    'choices' => [
                        "form.contact.subject.informations_request" => "form.contact.subject.informations_request",
                        "form.contact.subject.be_recalled" => "form.contact.subject.be_recalled",
                        "form.contact.subject.estimate" => "form.contact.subject.estimate",
                    ]
                ])
                ->add('message', TextareaType::class, [
                    'label' => 'form.contact.message.label',
                    'attr' => [
                        'rows' => 7
                ],
                'constraints' => [new NotBlank()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "required" => false
        ]);
    }
}
