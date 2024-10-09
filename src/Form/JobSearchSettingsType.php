<?php

namespace App\Form;

use App\Entity\JobSearchSettings;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSearchSettingsType extends AbstractType
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
            ->add('city_autocomplete', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher une ville'
                ],
                'mapped' => false
            ])
            ->add('city', HiddenType::class, [
                'mapped' => false
            ])
            ->add('distance', NumberType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'aria-describedby' => 'distance',
                    'placeholder' => 'Distance'
                ]
            ])
            ->add('whatExclude', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Mots clés à exclure ex : alternance, stage, ...',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobSearchSettings::class,
        ]);
    }
}
