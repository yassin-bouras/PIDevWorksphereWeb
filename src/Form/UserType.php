<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            // Remove 'mdp' from the form; we'll handle it via plainPassword
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // This field is not directly linked to the entity
                'required' => $options['is_new'] ?? true, // Required for new users, optional for edits
                'label' => 'Password',
                'attr' => ['autocomplete' => 'new-password'], // Improves security
            ])
            ->add('role')
            ->add('adresse')
            ->add('sexe')
            ->add('imageprofil')
            ->add('status')
            ->add('salaireattendu', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('poste')
            ->add('salaire', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('experiencetravail', NumberType::class, [
                'required' => false,
                'html5' => true,
            ])
            ->add('departement')
            ->add('competence')
            ->add('nombreprojet', NumberType::class, [
                'required' => false,
                'html5' => true,
            ])
            ->add('budget', NumberType::class, [
                'required' => false,
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01'],
            ])
            ->add('departementgere')
            ->add('ansexperience', NumberType::class, [
                'required' => false,
                'html5' => true,
            ])
            ->add('specialisation')
            ->add('banned')
            ->add('messagereclamation')
            ->add('numtel', NumberType::class, [
                'required' => false,
                'html5' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => true, // Default to true for new users; override in edit action
        ]);
    }
}
