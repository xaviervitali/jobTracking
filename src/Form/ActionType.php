<?php

namespace App\Form;

use App\Repository\ActionRepository;
use App\Repository\ResponseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType
{

    public function __construct(private ActionRepository $actionRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupérer les catégories depuis le repository
        $actions = $this->actionRepository->findAll();

        // Créer un tableau [ 'label' => 'value' ] pour les boutons radio
        $choices = [];
        foreach ($actions as $action) {
            $choices[$action->getName()] = $action->getId();
        }
        ksort($choices);

        // Ajout du champ radio avec des choix dynamiques
        $builder
            ->add('name', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => false, // pour rendre les options sous forme de boutons radio
                'multiple' => false, // assure que ce ne soit pas un champ de case à cocher, "
                'label' => false,
                'attr' => ['class' => 'form-select '],
                'label_attr' => ['class' => 'form-check-label'],
            ])
            ->add('createdAt', DateType::class, [
                'mapped'=>false,
                'label'=>false,    
                'data' => new \DateTime(),
                'widget' => 'single_text', // Permet l'affichage d'un input HTML5 de type date
                'attr' => ['class' => 'form-control '],
                // 'html5' => true, // Activer le contrôle des dates au niveau du navigateur
                // 'years' => range(date('Y') - 10, date('Y'))
            ]); // Limite la sélection d'années]);
    }
}
