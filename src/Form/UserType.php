<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('email', EmailType::class, [
        'label' => false, 
        'attr' => [
            'class' => 'form__input form-control my-1', 
            'placeholder' => 'Adresse Email', 
            'autocomplete' => 'email'
        ]
    ])
    ->add('firstname', TextType::class, [
        'label' => false, 
        'attr' => [
            'class' => 'form__input form-control my-1', 
            'placeholder' => 'Prénom'
        ]
    ])
    ->add('lastname', TextType::class, [
        'label' => false, 
        'attr' => [
            'class' => 'form__input form-control my-1', 
            'placeholder' => 'Nom'
        ]
    ])
    ->add('password', RepeatedType::class, [
        'type' => PasswordType::class,
        'first_options' => [
            'label' => false,
            'attr' => [
                'class' => 'form__input form-control my-1', 
                'placeholder' => 'Mot de passe'
            ],
            'constraints' => [
                new Assert\Length(['min' => 8, 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.']),
                new Assert\Regex([
                    'pattern' => '/[A-Z]/',
                    'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[a-z]/',
                    'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[0-9]/',
                    'message' => 'Votre mot de passe doit contenir au moins un chiffre.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[\W]/',
                    'message' => 'Votre mot de passe doit contenir au moins un caractère spécial.',
                ]),
            ]
        ],
        'second_options' => [
            'label' => false,
            'attr' => [
                'class' => 'form__input form-control my-1', 
                'placeholder' => 'Confirmer le mot de passe'
            ]
        ],
        'invalid_message' => 'Les mots de passe doivent correspondre.',
        'required' => true,
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
