<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\Niveau;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullSigle',
                'label' => 'Promotion',
                'attr' => ['class' => 'has-select2']
            ])
            /*   ->add('anneeScolaire', EntityType::class, [
                'class' => AnneeScolaire::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Année scolaire',
                'attr' => ['class' => 'has-select2']
            ]) */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
