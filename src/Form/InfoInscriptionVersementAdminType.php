<?php

namespace App\Form;

use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\NaturePaiement;
use App\Entity\Utilisateur;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoInscriptionVersementAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePaiement', DateType::class, [
                'required' => true,

                'widget' => 'single_text',
                'label'   => 'Date de paiement',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('modePaiement', EntityType::class, [
                'class' => NaturePaiement::class,
                'required' => true,

                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required mode'],
                'choice_attr' => function (NaturePaiement $mode) {
                    return ['data-value' => $mode->getCode()];
                },
                'choice_label' => 'libelle',
                'label' => 'Mode de paiement',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add('banque', TextType::class, ['label' => 'Banque', "constraints" => array(
                //new NotNull(null, "S'il vous veillez renseigner le champs banque")
            )])
            ->add('tireur', TextType::class, ['label' => 'Tireur', "constraints" => array(
                //  new NotNull(null, "S'il vous veillez renseigner le champs tireur")
            )])
            ->add('contact', TextType::class, ['label' => 'Contact tireur', "constraints" => array(
                //new NotNull(null, "S'il vous veillez renseigner le champs contact")
            )])

            ->add('numeroCheque', TextType::class, ['label' => 'Numéro chèque', "constraints" => array(
                // new NotNull(null, "S'il vous veillez renseigner le champs tireur")
            )])
            ->add('dateCheque', DateType::class, [
                'required' => true,

                'widget' => 'single_text',
                'label'   => 'Date du chèque',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montant', TextType::class, ['label' => 'Montant',  'attr' => ['class' => 'input-money input-mnt']]);


        /* $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-primary btn-sm', 'data-bs-dismiss' => 'modal']])
        ->add('save', SubmitType::class, ['label' => 'Payer', 'attr' => ['class' => 'btn btn-primary btn-ajax btn-sm']]); */
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoInscription::class,
        ]);
    }
}
