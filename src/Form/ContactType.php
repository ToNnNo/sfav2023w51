<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', options: [
                'label' => 'form.user.lastname.label',
            ])
            ->add('firstname', options: [
                'label' => 'form.user.firstname.label',
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
            ->add('picture', DropzoneType::class, [
                'label' => 'form.user.picture.label',
                'mapped' => false
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
