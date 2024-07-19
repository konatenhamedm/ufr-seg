<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\CoursParent;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursParentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeScolaire = $options['anneeScolaire'];

        $builder
            ->add('classe', EntityType::class, [
                /* 'placeholder' => 'choisissez une classe', */
                'class' => Classe::class,
                'choice_label' => 'getClasseAnneeScolaire',
                'attr' => ['class' => 'has-select2 form-select classe'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);
                },
            ])
            ->add(
                'cours',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => CoursType::class,
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
            'data_class' => CoursParent::class,
        ]);
        $resolver->setRequired(['anneeScolaire']);
    }
}
