<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\Niveau;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeScolaire = $options['anneeScolaire'];
        $builder
            ->add('libelle')
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'label' => "Niveau",
                'choice_label' => 'getFullCodeLibelle',
                'attr' => ['class' => 'has-select2 form-select niveau'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);
                },

            ])
            /*  ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullLibelle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ]) */
            ->add('anneeScolaire', EntityType::class, [
                'class' => AnneeScolaire::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Année scolaire',
                'attr' => ['class' => 'has-select2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
        $resolver->setRequired(['anneeScolaire']);
    }
}
