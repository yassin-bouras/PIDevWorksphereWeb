<?php

namespace App\Form;

use App\Entity\Offre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'attr' => [
                    'placeholder' => 'Entrez le titre de l\'offre...',
                ],
                'label' => 'Titre',
            ])
            // ->add('description')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 5, // Adjust the number of rows for the textarea
                    'placeholder' => 'Entrez la description de l\'offre...',
                ],
                'label' => 'Description',
            ])
            ->add('type_contrat', null, [
                'attr' => [
                    'placeholder' => 'Entrez le type de contrat (ex: CDI, CDD, Stage, FreeLancer...)',
                ],
                'label' => 'Type de Contrat',
            ])
            ->add('salaire', null, [
                'attr' => [
                    'placeholder' => 'Entrez le salaire proposé...',
                ],
                'label' => 'Salaire',
            ])
            ->add('lieu_travail', null, [
                'attr' => [
                    'placeholder' => 'Entrez le lieu de travail...',
                ],
                'label' => 'Lieu de Travail',
            ])
            ->add('date_publication', null, [
                'widget' => 'single_text'
            ])
            ->add('date_limite', null, [
                'widget' => 'single_text'
            ])
            ->add('statut_offre', null, [
                'attr' => [
                    'placeholder' => 'Entrez le statut de l\'offre (ex: Ouverte, En cours...)',
                ],
                'label' => 'Statut de l\'Offre',
            ])
            ->add('experience', null, [
                'attr' => [
                    'placeholder' => 'Entrez l\'expérience requise...',
                ],
                'label' => 'Expérience Requise',
            ])
            /*->add('users', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
'multiple' => true,
            ])
        ;*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
