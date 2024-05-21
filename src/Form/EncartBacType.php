<?php

namespace App\Form;

use App\Entity\EncartBac;
use App\Entity\Etudiant;
use App\Entity\Fichier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class EncartBacType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule', TextType::class, [
                'label' => 'Matricule Bac',
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ matricule")
                )
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro table Bac',
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ numero de table")
                )
            ])
            ->add('annee', TextType::class, [
                'label' => "Année d'obtention",
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ année d'obtention du bac")
                )
            ])
            ->add('serie', TextType::class, [
                'label' => 'Série Bac',
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ série du bac")
                )
            ])
            ->add(
                'bac',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Diplome',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EncartBac::class,
            'doc_required' => true,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
