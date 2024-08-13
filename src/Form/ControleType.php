<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Controle;
use App\Entity\Cours;
use App\Entity\Matiere;
use App\Entity\Semestre;
use App\Entity\Session;
use App\Entity\TypeControle;
use App\Entity\UniteEnseignement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControleType extends AbstractType
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($this->user->getPersonne()->getFonction()->getCode());
        $anneeScolaire = $options['anneeScolaire'];

        $builder

            ->add('classe', EntityType::class, [
                /* 'placeholder' => 'choisissez une classe', */
                'class' => Classe::class,
                'choice_label' => 'id',
                'attr' => ['class' => 'has-select2 form-select classe'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);
                },
            ])

            /*  ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'required' => false,
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')

                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => 'libelle',
                //'label' => 'Reponsable de niveau',
                'attr' => ['class' => 'has-select2 form-select']
            ]) */
            ->add('matiere', EntityType::class, [
                //'placeholder' => 'choisissez une matiere',
                'class' => Matiere::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select matiere']
            ])
            ->add('ue', EntityType::class, [
                /*   'label' => "Unité d'enseignement",
                'placeholder' => "choisissez une unité d'enseignement", */
                'class' => UniteEnseignement::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select ue']
            ])

            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);
                },
            ])
            ->add(
                'notes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => NoteType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add(
                'groupeTypes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => GroupeTypeType::class,
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
            'data_class' => Controle::class,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired(['anneeScolaire']);
    }
}
