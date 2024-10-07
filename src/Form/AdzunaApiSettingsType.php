<?php

namespace App\Form;

use App\Entity\AdzunaApiSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdzunaApiSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('what', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Intitulé de poste ex : Ouvrier polyvalent, Développeur, ...'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ville'
                ]
            ])
            // ->add('', TextType::class, [
            //     'label' => false, 
            //     'attr' => [
            //         'class' => 'form-control', 
            //         'placeholder' => 'Intitulé de poste'
            //     ])
            ->add('distance', NumberType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'aria-describedby'=>'distance',
                    'placeholder' => 'Distance'
                ]
            ])
            ->add('whatExclude', TextType::class, [
                'label' => false,
                'required'=>false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Mots clés à exclure ex : alternance, stage, ...',
                ]
            ])
            // ->add('whatOr')
            // ->add('user', EntityType::class, [utf8mb4_unicode_ci
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdzunaApiSettings::class,
        ]);
    }
}
