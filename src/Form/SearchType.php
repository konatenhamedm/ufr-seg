<?php

namespace App\Form;

use App\Attribute\Search;
use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\Employe;
use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\NaturePaiement;
use App\Entity\Niveau;
use App\Entity\TypeFrais;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'libelle',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'libelle',
                'label' => 'Filiere',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'libelle',
                'label' => 'Classe',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('typeFrais', EntityType::class, [
                'class' => TypeFrais::class,
                'choice_label' => 'libelle',
                'label' => 'Type frais',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('mode', EntityType::class, [
                'class' => NaturePaiement::class,
                'choice_label' => 'libelle',
                'label' => 'Mode de paiement',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date début',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'form-control-sm datepicker no-auto'],
            ])
            ->add('caissiere', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'getNomComplet',
                'label' => 'Caissière',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.personne', 'p')
                        ->join('p.fonction', 'f')
                        ->andWhere('f.code = :caissiere')
                        ->setParameter('caissiere', 'CAI')
                        ->orderBy('c.id', 'DESC');
                },
                'placeholder' => '---',
                'choice_attr' => function (Utilisateur $user) {
                    return ['data-type' => $user->getId()];
                },
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ]);
        $builder->add('imprime', SubmitType::class, ['label' => 'Sauvegarder', 'attr' => ['class' => 'btn btn-success  btn-filtre-imprime ', 'target' => '_blank', "data-target" => "_blank"]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
