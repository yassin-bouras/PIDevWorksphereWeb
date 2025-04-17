<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => $options['is_new'] ?? true,
                'label' => 'Mot de passe',
            ])
            ->add('adresse')
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'HOMME',
                    'Femme' => 'FEMME',
                ],
            ])
            ->add('salaireattendu', NumberType::class, [
                'required' => true,
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('imageprofil', FileType::class, [
                'label' => 'Image de profil',
                'mapped' => false,
                'required' => false,
            ])
            ->add('role', HiddenType::class, [
                'data' => 'Candidat',
                'mapped' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
