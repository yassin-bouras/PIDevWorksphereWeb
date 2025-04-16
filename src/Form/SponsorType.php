<?php

namespace App\Form;

use App\Entity\Sponsor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSponso')
            ->add('prenomSponso')
            ->add('emailSponso')
            ->add('budgetSponso')
            ->add('classement')
            ->add('BudgetApresReduction')
            ->add('secteurSponsor', TextType::class, [ // Ajout du champ secteurSponsor
                'label' => 'Secteur d\'activité',
                'required' => false, // Modifier à true si nécessaire
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Secteur d\'activité du sponsor'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsor::class,
        ]);
    }
}