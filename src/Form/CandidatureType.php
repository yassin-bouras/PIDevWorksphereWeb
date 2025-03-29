<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\Offre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cv')
            /////////////////////////
            // ->add('cv', FileType::class, [
            //     'label' => 'CV (PDF file)',
            //     'mapped' => true,
            //     'required' => true,
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '1024k',
            //             'mimeTypes' => [
            //                 'application/pdf',
            //                 'application/x-pdf',
            //             ],
            //             'mimeTypesMessage' => 'Please upload a valid PDF document',
            //         ])
            //     ],
            // ])
            /////////////////////////
            ->add('lettre_motivation')
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
