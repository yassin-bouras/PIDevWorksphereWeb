<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\EntityRepository;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_equipe')
           ->add('imageEquipe', FileType::class, [
            'label' => 'Image du equipe',
            'mapped' => false,  // Pas lié directement à la base de données
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '10M',
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                ])
            ]
        ])
         
    /*->add('users', EntityType::class, [
        'class' => User::class,
        'choice_label' => 'nom', 
        'multiple' => true,
        'expanded' => false,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.role = :role')
                ->setParameter('role', 'Employe');
        },
        'attr' => [
            'class' => 'select2', 
        ],
    ])
;*/

->add('users', EntityType::class, [
    'label' => 'Employés',
    'class' => User::class,
    'choice_label' => function(User $user) {
        return $user->getNom() . ' ' . $user->getPrenom();
    },
    'multiple' => true,
    'expanded' => false,
    'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', 'Employe')
            ->orderBy('u.nom', 'ASC');
    },
    'attr' => [
        'class' => 'select2-enhanced',
        'data-placeholder' => 'Sélectionnez des employés...'
    ],
    'placeholder' => '',
]);

}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}