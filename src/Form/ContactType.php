<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', options: [
                'label' => 'form.user.lastname.label',
                'row_attr' => [
                    'class' => "mb-0"
                ]
            ])
            ->add('firstname', options: [
                'label' => 'form.user.firstname.label',
                'row_attr' => [
                    'class' => "mb-0"
                ]
            ])
            ->add('email', options: [
                'label' => 'form.user.email.label',
            ])
            ->add('phone', options: [
                'label' => 'form.user.phone.label'
            ])
            ->add('birthday', options: [
                'label' => 'form.user.birthday.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
