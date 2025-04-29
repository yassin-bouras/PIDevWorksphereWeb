<?php

namespace App\Form;

use App\Entity\Evennement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Importez TextType

class EvennementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEvent')
            ->add('descEvent')
            ->add('dateEvent', null, [
                'widget' => 'single_text'
            ])
            ->add('lieuEvent')
            ->add('capaciteEvent')
            ->add('typeEvent', TextType::class, [ // Ajoutez le champ typeEvent
                'label' => 'Type d\'événement',
                'required' => false, // Ou true si le type est obligatoire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Conférence, Atelier, Concert...'
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evennement::class,
        ]);
    }
}