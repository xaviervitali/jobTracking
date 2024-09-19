<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobTrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $date =  new DateTimeImmutable();
        $dateIso8601 = $date->format('c');
        $dateMax = substr($dateIso8601, 0, -15);
        // $jobTracking
        // ->setJob($job)
        // ->setAction($action)
        // ->setUser($security->getUser())
        // ->setCreatedAt(new DateTimeImmutable());

        $builder
        ->add('createdAt', DateType::class, [
            'label' => 'Date',
            'widget' => 'single_text', // Permet l'affichage d'un input HTML5 de type date
            'attr' => ['class' => 'form-control'],
            'html5' => true, // Activer le contrôle des dates au niveau du navigateur
            'years' => range(date('Y') - 10, date('Y')), // Limite la sélection d'années
        ])
        ->add('action', EntityType::class, [
            'class' => Action::class,
            'choice_label' => 'name', // Le label qui sera affiché
            'label' => 'Action',
            'attr' => ['class' => "form-select"], // Utilisé pour les "select", mais pas nécessaire ici avec les radio
            'choice_attr' => function (Action $action) {
                return ['class' => 'form-check-input']; // Applique la classe aux boutons radio
            },
            'expanded' => true, // Options sous forme de boutons radio
            'multiple' => false, // Un seul bouton peut être sélectionné
            'label_attr' => ['class' => 'form-check-label'], // Classe CSS pour les labels
        ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobTracking::class,
        ]);
    }
}
