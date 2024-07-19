<?php

namespace App\Form;

use App\Entity\NoteExamen;
use App\Entity\ValeurNoteExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValeurNoteExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note')
            /*   ->add('noteEntity', EntityType::class, [
                'class' => NoteExamen::class,
'choice_label' => 'id',
            ]) */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ValeurNoteExamen::class,
            'allow_extra_fields' => true

        ]);
    }
}
