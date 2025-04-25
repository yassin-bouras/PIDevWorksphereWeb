<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Technique' => 'technique',
                    'Administratif' => 'administratif',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Choisir un type',
            ])
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'placeholder' => 'Choisir un employé',
                'query_builder' => fn(UserRepository $ur) =>
                $ur->createQueryBuilder('u')
                    ->where('u.role = :role')
                    ->setParameter('role', 'Employe'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
