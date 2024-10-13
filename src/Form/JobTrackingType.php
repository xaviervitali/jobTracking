<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\JobTracking;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            'attr' => ['class' => 'form-control '],
            'html5' => true, // Activer le contrôle des dates au niveau du navigateur
            'years' => range(date('Y') - 10, date('Y')), // Limite la sélection d'années
        ])
        ->add('action', EntityType::class, [
            
            'class' => Action::class,
            'choice_label' => 'name', // Assuming 'name' is the property to display
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                          ->orderBy('a.name', 'ASC');
            },
            'label' => 'Action',
            'attr' => ['class' => "form-select mb-3"], // Utilisé pour les "select", mais pas nécessaire ici avec les radio
            'expanded' => false, // Options sous forme de boutons radio
            'multiple' => false, // Un seul bouton peut être sélectionné
        ])
// ->add('id', TextType::class, ['attr'=>['class'=>'d-none']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobTracking::class,
        ]);
    }
}
