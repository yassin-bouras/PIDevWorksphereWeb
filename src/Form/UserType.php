<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('mdp')
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
        ]);
    }
}
