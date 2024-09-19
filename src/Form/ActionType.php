<?php

namespace App\Form;

use App\Repository\ActionRepository;
use App\Repository\ResponseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'expanded' => true, // pour rendre les options sous forme de boutons radio
                'multiple' => false, // assure que ce ne soit pas un champ de case à cocher, "
                'label' => false,
                'label_attr' => ['class' => 'form-check-label'],
            ]);
    }
}
