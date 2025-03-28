<?php

namespace App\Form;

use App\Entity\HistoriqueEntretien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoriqueEntretienType extends AbstractType
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
            ->add('action')
            ->add('date_action', null, [
                'widget' => 'single_text'
            ])
            ->add('employe_id')
            ->add('feedbackId')
            ->add('candidatId')
            ->add('idOffre')
            ->add('idCandidature')
            ->add('entretien_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HistoriqueEntretien::class,
        ]);
    }
}
