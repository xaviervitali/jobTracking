<?php
namespace App\Form;

use App\Entity\CV;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cvFile', FileType::class, [
                'label' => 'Télécharger votre CV (PDF)',

                // non-mappé signifie que ce champ n'est pas directement lié à l'entité
                'mapped' => false,

                // rendre le champ optionnel si vous ne souhaitez pas le rendre obligatoire
                'required' => false,

                // Contraintes de validation du fichier (taille maximale et type MIME)
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide',
                    ])
                ],
                'attr'=>['class' =>'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CV::class,
        ]);
    }
}
