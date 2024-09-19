<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Note;
use PostitColors;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $colors = [
            'Rose' => '#ff7eb9',
            'Rose clair' => '#ff65a3',
            'Cyan' => '#7afcff',
            'Jaune' => '#feff9c',
            'Jaune clair' => '#fff740'
        ];

        $builder

            ->add('content', TextareaType::class, ['label' => false, 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Nouvelle note']])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'id',
                'attr' => ['class' => "d-none"],
                'label' => false
            ])
            ->add('color', ChoiceType::class, [
                'choices' => PostitColors::getColors(),
                'expanded' => true,
                'multiple' => false,
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
