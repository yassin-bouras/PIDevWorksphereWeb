<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('dateCreation', null, [
                'widget' => 'single_text',
                'label' => 'date début',
            ])
            ->add('deadline', null, [
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'À faire' => 'À faire',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                ],
                'placeholder' => 'Choisissez un statut',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('priorite')
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'nom',
            ])
            ->add('assignee', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'Employe')
                        ->orderBy('u.nom', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
