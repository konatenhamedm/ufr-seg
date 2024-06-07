<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Employe;
use App\Entity\Niveau;
use App\Entity\Promotion;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('libelle')
            /*      ->add('numero') */
            ->add('numero', TextType::class, ['attr' => ['class' => 'input-money credit', 'data-max' => 200]])

            ->add('anneeScolaire', EntityType::class, [
                'class' => anneeScolaire::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Annéree scolaire',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ])

            ->add(
                'frais',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => FraisType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add('responsable', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.fonction', 'f')
                        ->andWhere('f.code = :code')
                        ->setParameter('code', 'DR')
                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => 'nomComplet',
                'label' => 'Reponsable de niveau',
                'attr' => ['class' => 'has-select2']
            ]);
        $builder->get('numero')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
