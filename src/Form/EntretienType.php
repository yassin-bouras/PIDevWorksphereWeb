<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\Entretien;
use App\Entity\Feedback;
use App\Entity\Offre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntretienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('date_entretien', null, [
                'widget' => 'single_text'
            ])
            ->add('heure_entretien', null, [
                'widget' => 'single_text'
            ])
            ->add('type_entretien')
            ->add('status')
            ->add('candidatId')
            ->add('user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('feedback', EntityType::class, [
                'class' => Feedback::class,
'choice_label' => 'id',
            ])
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
'choice_label' => 'id',
            ])
            ->add('candidature', EntityType::class, [
                'class' => Candidature::class,
'choice_label' => 'id',
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
