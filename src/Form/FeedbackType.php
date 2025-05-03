<?php

namespace App\Form;

use App\Entity\Entretien;
use App\Entity\Feedback;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')

            ->add('q1', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Le candidat s est-il bien présenté  ?',
                'choices' => [
                    'Bien' => 2,
                    'Moyen' => 1,
                    'Faible' => 0,
                ],
                'expanded' => true, 
            ])
            ->add('q2', ChoiceType::class, [
                'mapped' => false,
                'label' => 'A-t-il démontré une bonne compréhension du poste  ?',
                'choices' => [
                    
                    'Bien' => 2,
                    'Moyen' => 1,
                    'Faible' => 0,
                ],
                'expanded' => true,
            ])
            ->add('q3', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Le candidat a-t-il démontré une bonne maîtrise des compétences techniques requises pour le poste',
                'choices' => [
                    
                    'Bien' => 2,
                    'Moyen' => 1,
                    'Faible' => 0,
                ],
                'expanded' => true,
            ])
            ->add('q4', ChoiceType::class, [
                'mapped' => false,
                'label' => 'A-t-il su résoudre les cas pratiques / exercices techniques proposés',
                'choices' => [
                    'Bien' => 2,
                    'Moyen' => 1,
                    'Faible' => 0,
                ],
                'expanded' => true,
            ])
            ->add('q5', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Son niveau technique est-il adapté au poste',
                'choices' => [
                    'Bien' => 2,
                    'Moyen' => 1,
                    'Faible' => 0,
                ],
                'expanded' => true,
            ])
            
            ->add('entretien', HiddenType::class, [
                'mapped' => false, 
            ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
