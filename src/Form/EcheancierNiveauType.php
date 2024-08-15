<?php

namespace App\Form;

use App\Entity\EcheancierNiveau;
use App\Entity\Niveau;
use App\Form\DataTransformer\ThousandNumberTransformer;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;

class EcheancierNiveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TypeIntegerType::class, [
                'attr' => ['class' => 'numero', 'min' => 1],
            ])
            ->add('dateVersement', DateType::class, [
                'widget' => 'single_text',
                'label'   => false,
                // 'format'  => 'dd/MM/yyyy',
                'html5' => true,
                //'data'   => new DateTime(),

                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner la date écheancier")
                )
                //  'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montant');
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EcheancierNiveau::class,
        ]);
    }
}
