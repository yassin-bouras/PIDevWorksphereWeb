<?php

namespace App\Form;

use App\Entity\EvenementSponsor;
use App\Entity\Evennement;
use App\Entity\Sponsor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementSponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('evenement', EntityType::class, [
                'class' => Evennement::class,
                'choice_label' => 'nomEvent', // Modification ici
                'label' => 'Événement',
            ])
            ->add('sponsor', EntityType::class, [
                'class' => Sponsor::class,
                'choice_label' => 'nomSponso', // Modification ici
                'label' => 'Sponsor',
            ])
            ->add('datedebutContrat', null, [
                'widget' => 'single_text',
                'label' => 'Date de début du contrat',
            ])
            ->add('duree', null, [
                'label' => 'Durée',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EvenementSponsor::class,
        ]);
    }
}