<?php

namespace App\Form;

use App\Entity\ControleExamen;
use App\Entity\GroupeTypeExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeTypeExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('dateCompo', DateType::class, [
                'widget' => 'single_text',
                'label'   => false,
                'format'  => 'dd/MM/yyyy',
                'empty_data' => '',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add(
                'max',
                ChoiceType::class,
                [

                    'label' => false,
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        '10' => '10',
                        '20' => '20',
                        '40' => '40',

                    ]),

                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupeTypeExamen::class,
            'allow_extra_fields' => true
        ]);
    }
}
