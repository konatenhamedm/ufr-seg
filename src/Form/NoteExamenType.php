<?php

namespace App\Form;

use App\Entity\ControleExamen;
use App\Entity\Etudiant;
use App\Entity\NoteExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroEtape', IntegerType::class, [
                'label' => false,
                'mapped' => false
            ])
            ->add(
                'valeurNoteExamens',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => ValeurNoteExamenType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add('rang')
            ->add('exposant')
            ->add('moyenneUe')
            ->add('moyenneConrole')
            ->add('decision')

            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'getNomComplet',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteExamen::class,
        ]);
    }
}
