<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\FraisInscription;
use App\Entity\Inscription;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('dateInscription')
            ->add('montant')
            ->add('datePaiement')
            ->add('niveauEtudiant')
            ->add('etudiant')
            ->add('niveau') */
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'libelle',
            ])
            ->add('montant', TextType::class, ['attr' => ['class' => 'input-money input-mnt total']])
            ->add(
                'fraisInscriptions',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => FraisInscriptionType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add(
                'echeanciers',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => EcheancierType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            );
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());

        /*  $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-primary btn-sm', 'data-bs-dismiss' => 'modal']])
            ->add('save', SubmitType::class, ['label' => 'Enregister', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])
            ->add('passer', SubmitType::class, ['label' => "Valider l'échéancier ", 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']])
            ->add('rejeter', SubmitType::class, ['label' => "Réjeter l'échéancier", 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
            ->add('retour', SubmitType::class, ['label' => "Soumettre à nouveau", 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
            ->add('resoumettre', SubmitType::class, ['label' => 'Soumettre', 'attr' => ['class' => 'btn btn-warning btn-ajax btn-sm']]); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
