<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\Entretien;
use App\Entity\Feedback;
use App\Entity\Offre;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntretienType extends AbstractType
{


    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }



    

  
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $candidats = $this->userRepository->findByRoleQueryBuilder('Candidat')->getQuery()->getResult();

        $candidatChoices = [];
        foreach ($candidats as $candidat) {
            $candidatChoices[$candidat->getNom() . ' ' . $candidat->getPrenom()] = $candidat->getId();
        }

        $builder
        ->add('titre')
            ->add('description')
            ->add('date_entretien', null, [
                'widget' => 'single_text'
            ])
            ->add('heureentretien', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => true,
            ])
            ->add('type_entretien', ChoiceType::class, [
                'choices' => [
                    'En présentiel' => 'EN_PRESENTIEL',
                    'En visio' => 'EN_VISIO',
                ],
                'placeholder' => 'Choisir le type d\'entretien',
            ])
            ->add('candidatId', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1, 
                ],
            ])     
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => fn() => $this->userRepository->findByRoleQueryBuilder('Employe'),
                'choice_label' => fn(User $user) => $user->getNom() . ' ' . $user->getPrenom(),
                'placeholder' => 'Sélectionnez un employé',
                'label' => 'Employé',
                'attr' => ['class' => 'form-control']
            ])

            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'titre',
                'placeholder' => 'Sélectionnez une offre',
                'label' => 'Offre',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'offre-select'
                ]
            ])
            
            
            ;
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entretien::class,
        ]);
    }
}
