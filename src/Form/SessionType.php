<?php

namespace App\Form;

use App\Entity\Promotion;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            /*  ->add('numero') */
            ->add(
                'numero',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un numéro',
                    'label' => 'Numéro',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        '1' => '1',
                        '2' => '2',

                    ]),
                ]
            )
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullSigle',
                'label' => 'Promotion',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('dateDebut',  DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date debut',
                'format'  => 'dd/MM/yyyy',
                'empty_data' => '',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('dateFin',  DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'empty_data' => '',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
