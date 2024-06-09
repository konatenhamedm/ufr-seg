<?php

namespace App\Form;

use App\Entity\ControleExamen;
use App\Entity\Matiere;
use App\Entity\Promotion;
use App\Entity\Session;
use App\Entity\UniteEnseignement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
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
        //dd($this->user);

        $builder
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'label' => "Niveau",
                'choice_label' => 'getFullSigle',
                'attr' => ['class' => 'has-select2 form-select promotion']

            ])

            ->add('ue', EntityType::class, [
                /*   'label' => "Unité d'enseignement",
                'placeholder' => "choisissez une unité d'enseignement", */
                'class' => UniteEnseignement::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select ue']
            ])

            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select session']
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
    }
}
