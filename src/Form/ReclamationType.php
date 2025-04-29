<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextType::class)
            ->add('type', TextType::class)
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // or 'username', 'email', etc. — use what fits best
                'placeholder' => 'Choose an employee',
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
