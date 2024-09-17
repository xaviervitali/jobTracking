<?php

namespace App\Form;

use App\Entity\Job;
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
        $builder
            ->add('recruiter', TextType::class, ['label'=>'Employeur', 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Nom de l\'employeur']])
            ->add('title', TextType::class, ['label'=>'Intitulé','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Titre de l\'annonce']])
            ->add('source', TextType::class, ['label'=>'Source','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Source de l\'annonce']])
            ->add('offerDescription', TextareaType::class, ['label'=>'Détail','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Texte de l\'annonce']])
            ->add('created_at', DateType::class, ['label'=>'Date','attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Candidature le', 'value'=>date('Y-m-d')]])
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
