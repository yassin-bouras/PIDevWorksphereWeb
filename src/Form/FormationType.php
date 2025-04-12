<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez insérer une image.']),
                    new Image([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF).',
                    ])
                ]
            ])
            ->add('titre')
            ->add('description')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
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
            ->add('nb_place', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'placeholder' => 'Ex: 25',
                    'class' => 'form-control',
                    'min' => 1 
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 'présentiel',
                    'Distanciel' => 'distanciel',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}