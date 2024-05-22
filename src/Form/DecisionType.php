<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Decision;
use App\Entity\Preinscription;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DecisionType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('dateCreation', DateType::class, [
                'required' => true,
                'mapped' => true,
                'widget' => 'single_text',
                'label'   => 'Date',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire', 'required' => false, 'empty_data' => 'RAS',
                'attr' => array('rows' => '1')
            ])
            // ->add('user', TextType::class, ['label' => 'Utilisateur', 'mapped' => false])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'required' => false,
                //'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => 'getNomComplet',
                'label' => 'Dossier suivi par',
                'attr' => ['class' => 'has-select2 ']
            ])
            ->add(
                'decision',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir une decision',
                    'label' => 'Décision',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'Valider' => 'Valider',
                        'Recaler' => 'Recaler',
                        'Attente_info' => 'En attente informations',
                    ]),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Decision::class,
        ]);
    }
}
