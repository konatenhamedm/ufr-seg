<?php

namespace App\Form;

use App\Entity\Niveau;
use App\Entity\Promotion;
use App\Entity\Semestre;
use App\Entity\UniteEnseignement;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniteEnseignementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeUe')
            ->add('libelle')
            /*  ->add('coef', TextType::class, [
                'label' => 'Nombre de crédit'
            ]) */
            ->add('coef', TextType::class, [
                'label' => 'Nombre de crédit',
                'attr' => ['class' => 'input-money input-note', 'data-max' => 30]
            ])
            ->add(
                'attribut',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un attribut',
                    'label' => 'Attribut',
                    'required'     => true,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'Majeur' => 'Majeur',
                        'Mineur' => 'Mineur',
                        'Libre' => 'Libre',
                    ]),
                ]
            )
            ->add('volumeHoraire', TextType::class, [
                'label' => 'Volume horaire',
                'attr' => ['class' => 'input-money input-note', 'data-max' => 100]
            ])
            /*  ->add('volumeHoraire') */
            /*   ->add('totalCredit') */
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 '],
            ])
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'label' => 'Niveau',
                'choice_label' => 'getFullSigleNiveau',
                'label_attr' => ['class' => 'label-required'],
                'attr' => ['class' => 'niveau has-select2 '],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                },
            ])
            ->add(
                'matiereUes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => MatiereUeType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            );
        $builder->get('coef')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('volumeHoraire')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UniteEnseignement::class,
        ]);
    }
}
