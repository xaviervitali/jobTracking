<?php

namespace App\Form;

use App\Entity\Job;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $date =  new DateTimeImmutable();
        $dateIso8601 = $date->format('c');
        $dateMax = substr($dateIso8601, 0, -6);

        $builder
            ->add('recruiter', TextType::class, ['label'=>'Employeur', 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Nom de l\'employeur']])
            ->add('title', TextType::class, ['label'=>'Intitulé de l\'offre','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Titre de l\'annonce']])
            ->add('source', TextType::class, ['label'=>'Source de l\'offre','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Source de l\'annonce']])
            ->add('offerDescription', TextareaType::class, ['label'=>'Description du poste','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Texte de l\'annonce','rows' => '10']])
            ->add('created_at', DateType::class, ['label'=>'Date de candidature','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Candidature le',  'max'=>  $dateMax]])
            ->add('coverLetter', TextareaType::class, ['label'=>'Lettre de motivation',  'required' => false, 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Lettre de motivation', 'required'=>false]])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}