<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostulerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre nom',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'prenom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre prénom',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre Email',
                ],
            ])
            ->add('cv', TextType::class, [
                'label' => 'CV',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez votre CV',
                    'minlength' => 100,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
