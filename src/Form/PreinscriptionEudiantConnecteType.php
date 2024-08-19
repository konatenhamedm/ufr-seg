<?php

namespace App\Form;

use App\Entity\Niveau;
use App\Entity\Personne;
use App\Entity\Preinscription;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscriptionEudiantConnecteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeScolaire = $options['anneeScolaire'];
        $etudiant = $options['etudiant'];
        //dd($anneeScolaire);


        /*
            ->add('niveau', EntityType::class, [
               'class' => Niveau::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'fullLibelle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ])*/

        if ($etudiant == null) {
            $builder->add(
                'niveau',
                EntityType::class,
                [
                    'class' => Niveau::class,
                    'choice_label' => 'getFullCodeLibelle',
                    'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                        return $er->findNiveauDisponible($anneeScolaire);
                    }
                ]
            );
        } else {
            $builder->add(
                'niveau',
                EntityType::class,
                [
                    'class' => Niveau::class,
                    'choice_label' => 'getFullCodeLibelle',
                    'query_builder' => function (EntityRepository $er) use ($anneeScolaire, $etudiant) {
                        return $er->findNiveauDisponibleAdmin($anneeScolaire, $etudiant);
                    }
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preinscription::class,
            'validate' => false,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->getEtat() == 'rejet') {
                    return ['Default', 'rejet-preinscription'];
                }
                return ['Default'];
            }
        ]);

        $resolver->setRequired('validate');
        $resolver->setRequired(['anneeScolaire']);
        $resolver->setRequired(['etudiant']);
    }
}
