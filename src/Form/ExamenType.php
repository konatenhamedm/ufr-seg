<?php

namespace App\Form;

use App\Entity\Examen;
use App\Entity\MatiereExamen;
use App\Entity\Niveau;
use App\Entity\Promotion;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExamenType extends AbstractType
{
    private $user;
    public function __construct(Security $user)
    {
        $this->user = $user->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        //dd($this->user);

        $anneeScolaire = $options['anneeScolaire'];
        //dd($anneeScolaire);
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('code', null, ['label' => 'Code'])
            ->add('dateExamen', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date de début',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            /*  ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullLibelleSigle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ]) */

            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'label' => 'Niveau',
                //'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) use ($anneeScolaire) {
                    return $sql = $er->createQueryBuilder('c')
                        ->andWhere("c.anneeScolaire = :annee")
                        ->setParameter('annee', $anneeScolaire);

                    if ($this->user->getPersonne()->getFonction()->getCode() == 'DR') {
                        $sql->innerJoin('c.responsable', 'res')
                            ->andWhere("res = :user")
                            ->setParameter('user', $this->user->getPersonne());
                    }
                },
                'choice_label' => 'getFullCodeAnneeScolaire',
                //'label' => 'Reponsable de niveau',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add(
                'matiereExamens',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => MatiereExamenType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examen::class,
        ]);
        $resolver->setRequired(['anneeScolaire']);
    }
}
