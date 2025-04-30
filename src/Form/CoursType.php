<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_c', TextType::class, [
            'label' => 'Nom du Cours',
           
        ])
        ->add('description_c', TextareaType::class, [
            'label' => 'Description du Cours',
            
        ])
        ->add('heure_debut', TimeType::class, [
            'widget' => 'single_text',
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('heure_fin', TimeType::class, [
            'widget' => 'single_text',
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
        ->add('date', DateType::class, [
            'label' => 'Date du cours',
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control'],
        ])
        ->add('photo', FileType::class, [
            'label' => 'Photo',
            'mapped' => false,
            'required' => false,
        
        ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
