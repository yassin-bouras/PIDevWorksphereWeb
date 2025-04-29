<?php

namespace App\Form;

use App\Entity\EvenementSponsor;
use App\Entity\Evennement;
use App\Entity\Sponsor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvenementSponsorType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sponsor = $options['sponsor'];
        $evenementOptions = [
            'class' => Evennement::class,
            'choice_label' => 'nomEvent',
            'label' => 'Événement',
            'attr' => [
                'class' => 'form-control',
                'data-secteur' => $sponsor ? $sponsor->getSecteurSponsor() : null
            ]
        ];

        // Si un sponsor est spécifié, on filtre les événements
        if ($sponsor) {
            $evenementOptions['choices'] = $this->getFilteredEvents($sponsor);
            $evenementOptions['placeholder'] = 'Sélectionnez un événement';
        }

        $builder
            ->add('evenement', EntityType::class, $evenementOptions)
            ->add('sponsor', EntityType::class, [
                'class' => Sponsor::class,
                'choice_label' => 'nomSponso',
                'label' => 'Sponsor',
                'disabled' => $sponsor !== null, // Désactivé si sponsor pré-sélectionné
                'attr' => ['class' => 'form-control'],
            ])
            ->add('datedebutContrat', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date de début du contrat',
            ])
            ->add('duree', ChoiceType::class, [
                'label' => 'Durée du contrat',
                'choices' => [
                    '3 Mois' => 'troisMois',
                    '6 Mois' => 'sixMois',
                    '1 An' => 'unAns',
                ],
                'placeholder' => 'Choisir une durée',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EvenementSponsor::class,
            'sponsor' => null,
        ]);
    }

    /**
     * Filtre les événements selon le secteur du sponsor et ceux non déjà associés
     */
    private function getFilteredEvents(Sponsor $sponsor): array
    {
        $evenementRepo = $this->em->getRepository(Evennement::class);
        $evenementSponsorRepo = $this->em->getRepository(EvenementSponsor::class);

        // Récupère tous les événements du même secteur que le sponsor
        $events = $evenementRepo->findBy(['typeEvent' => $sponsor->getSecteurSponsor()]);

        // Filtre ceux qui n'ont pas déjà ce sponsor
        return array_filter($events, function($event) use ($sponsor, $evenementSponsorRepo) {
            return !$evenementSponsorRepo->findOneBy([
                'evenement' => $event,
                'sponsor' => $sponsor
            ]);
        });
    }
}