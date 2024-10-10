<?php

namespace App\Form;

use App\Constants\EmploymentWebsites;
use App\Entity\Job;
use App\Entity\JobSource;
use App\Repository\JobSourceRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $dateMax = substr($dateIso8601, 0, -15);
        // $sites = new EmploymentWebsites()::getWebSites();

        $builder
            ->add('recruiter', TextType::class, ['label' => false, 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Employeur']])
            ->add('title', TextType::class, ['label' => false, 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Intitulé de l\'offre']])
            ->add('source', EntityType::class, [
                'class' => JobSource::class,
                'choice_label' => 'name' , 'query_builder' => function (JobSourceRepository $er) {
                    return $er->createQueryBuilder('j')
                              ->orderBy('j.name', 'ASC');
                },
                'attr' => ['class' => 'form-select disableable']
            ])
            ->add('offerDescription', TextareaType::class, ['label' => 'Description du poste', 'attr' => ['class' => "form-control mb-3 disableable", 
             'rows' => '25'
             ]])
            ->add('created_at', DateType::class, ['label' => 'Date de candidature', 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => false,  'max' =>  $dateMax]])
       

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
