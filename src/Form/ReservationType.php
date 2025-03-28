<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'titre', // Affiche le titre des formations dans le select
                'placeholder' => 'Sélectionnez une formation'
            ])
          
            ->add('motif_r', TextType::class)
            ->add('attente', TextType::class)
                    
            ->add('langue', ChoiceType::class, [
                'choices' => [
                    'Français' => 'fr',
                    'Anglais' => 'en',
                ],
                'expanded' => false,
                'multiple' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
