<?php

namespace App\Form;

use App\Entity\FraisInscription;
use App\Entity\Inscription;
use App\Entity\TypeFrais;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FraisInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', TextType::class, ['attr' => ['class' => 'input-money input-mnt autre_frais']])
            ->add('typeFrais', EntityType::class, [
                'class' => TypeFrais::class,
                'choice_label' => 'libelle',
            ])
            /*    ->add('blocEcheancier', EntityType::class, [
            'class' => BlocEcheancier::class,
'choice_label' => 'id',
        ]) */;
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FraisInscription::class,
        ]);
    }
}
