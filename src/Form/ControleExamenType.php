<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\ControleExamen;
use App\Entity\Matiere;
use App\Entity\Niveau;
use App\Entity\Promotion;
use App\Entity\Session;
use App\Entity\UniteEnseignement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControleExamenType extends AbstractType
{
    private $user;


    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options['anneeScolaire']);
        $anneeScolaire = $options['anneeScolaire'];

        $builder
        ->add('classe', EntityType::class, [
            /* 'placeholder' => 'choisissez une classe', */
            'class' => Classe::class,
            'choice_label' => 'libelle',
            'attr' => ['class' => 'has-select2 form-select classe'],
            'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                return $er->createQueryBuilder('c')
                    ->andWhere("c.anneeScolaire = :annee")
                    ->setParameter('annee', $anneeScolaire);
            },
        ])


            ->add('ue', EntityType::class, [
                /*   'label' => "Unité d'enseignement",
                'placeholder' => "choisissez une unité d'enseignement", */
                'class' => UniteEnseignement::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select ue']
            ])

            ->add('matiere', EntityType::class, [
                //'placeholder' => 'choisissez une matiere',
                'class' => Matiere::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select matiere']
            ])

            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select session'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.niveau', 'niveau')
                        ->andWhere("niveau.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);
                },
            ])
            ->add(
                'noteExamens',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => NoteExamenType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add(
                'groupeTypeExamens',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => GroupeTypeExamenType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ControleExamen::class,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired(['anneeScolaire']);
    }
}
