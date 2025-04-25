<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
        
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'date début',
            ])
            ->add('deadline', DateType::class, [
                'widget' => 'single_text', 
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                    'En Cours' => 'EnCours',
                ],
            ])
            ->add('imageProjet', FileType::class, [
                'label' => 'Image du projet',
                'mapped' => false, 
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                    ])
                ]
            ])
            /*->add('equipe', EntityType::class, [
                'class' => Equipe::class,
'choice_label' => 'nom_equipe',
            ])*/

            ->add('equipes', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom_equipe',
                'multiple' => true, 
                'expanded' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
