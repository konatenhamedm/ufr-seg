<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Filiere;
use App\Entity\Promotion;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'code',
                'label' => 'Filiere',
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
