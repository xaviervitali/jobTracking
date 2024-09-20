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


        $job = $options['job'] ?? null;

        $builder

            ->add('content', TextareaType::class, ['label' => false, 'attr' => ['class' => "form-control mb-3 disableable", 'placeholder' => 'Nouvelle note']])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'id',
                'data' => $job,
                'attr' => ['class' => "d-none"],
                'label' => false
            ])
            ->add('color', ChoiceType::class, [
                'choices' => PostitColors::getColors(), // Assurez-vous que cette méthode retourne un tableau du format attendu.
                'expanded' => true,  // Afficher sous forme de boutons radio
                'multiple' => false, // Assurer qu'une seule couleur peut être sélectionnée
                'label' => false,    // Ne pas afficher de label global
                'data' => '#feff9c', // Valeur par défaut (la couleur sélectionnée par défaut),
                'choice_attr' => function ($choice, $key, $index) {
                    // $choice est la valeur (#ff7eb9, #ff65a3, etc.)
                    // $key est l'intitulé ('Rose', 'Pink', etc.)
                    return ['data-color' => $choice]; // Ajouter data-color et un style en ligne
                },
       
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'job' => null, // Option pour passer un Job
        ]);
    }
}
