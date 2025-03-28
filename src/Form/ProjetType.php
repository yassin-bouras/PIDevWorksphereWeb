<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('datecréation', null, [
                'widget' => 'single_text'
            ])
            ->add('deadline', null, [
                'widget' => 'single_text'
            ])
            ->add('etat')
            ->add('imageProjet')
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
