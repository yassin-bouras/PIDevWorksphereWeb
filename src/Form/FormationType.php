<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo')
            ->add('titre')
            ->add('description')
            ->add('date', null, [
                'widget' => 'single_text'
            ])
            ->add('heure_debut', null, [
                'widget' => 'single_text'
            ])
            ->add('heure_fin', null, [
                'widget' => 'single_text'
            ])
            ->add('nb_place')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 'Présentiel',
                    'Distanciel' => 'Distanciel',
                ],
                'expanded' => false,
                'multiple' => false
            ]);
           
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
