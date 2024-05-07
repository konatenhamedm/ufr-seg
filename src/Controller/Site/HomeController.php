<?php

namespace App\Controller\Site;

use App\Controller\FileTrait;
use App\DTO\InscriptionDTO;
use App\Entity\BlocEcheancier;
use App\Entity\Classe;
use App\Entity\Echeancier;
use App\Entity\EcheancierProvisoire;
use App\Entity\Employe;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Fonction;
use App\Entity\FraisBloc;
use App\Entity\InfoEtudiant;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Entity\Niveau;
use App\Entity\NiveauEtudiant;
use App\Entity\Pays;
use App\Entity\Preinscription;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurGroupe;
use App\Form\CiviliteType;
use App\Form\EtudiantAdminNewType;
use App\Form\EtudiantAdminType;
use App\Form\EtudiantDocumentType;
use App\Form\EtudiantType;
use App\Form\InscriptionPayementType;
use App\Form\RegisterType;
use App\Form\UtilisateurInscriptionSimpleType;
use App\Form\UtilisateurInscriptionType;
use App\Form\UtilisateurType;
use App\Repository\ClasseRepository;
use App\Repository\EcheancierRepository;
use App\Repository\EmployeRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\FonctionRepository;
use App\Repository\FraisInscriptionRepository;
use App\Repository\FraisRepository;
use App\Repository\GroupeRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NaturePaiementRepository;
use App\Repository\NiveauEtudiantRepository;
use App\Repository\NiveauRepository;
use App\Repository\PaysRepository;
use App\Repository\PersonneRepository;
use App\Repository\PreinscriptionRepository;
use App\Repository\UtilisateurGroupeRepository;
use App\Repository\UtilisateurRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\SendMailService;
use App\Service\Service;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;

class HomeController extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_etudiant';
    private $em;
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }
    private function numero($code)
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Inscription::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ($code . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }


    #[Route('/liste/niveau/par/filiere/{id}',  methods: ['GET'])]
    public function getNiveau(Request $request, NiveauRepository  $niveauRepository, $id)
    {
        $response = new Response();
        $tabNiveaux = array();


        // $id = $request->get('id');

        if ($id) {


            $niveaux = $niveauRepository->findBy(['filiere' => $id]);
            // dd($frais);

            $i = 0;

            foreach ($niveaux as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabNiveaux[$i]['id'] = $e->getId();
                $tabNiveaux[$i]['libelle'] = $e->getLibelle();


                $i++;
            }

            $dataService = json_encode($tabNiveaux); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }
    #[Route('/liste/classe/par/niveau/{id}',  methods: ['GET'])]
    public function getClasse(Request $request, ClasseRepository  $classeRepository, $id)
    {
        $response = new Response();
        $tabClasse = array();


        // $id = $request->get('id');

        if ($id) {


            $classes = $classeRepository->findBy(['niveau' => $id]);
            // dd($frais);

            $i = 0;

            foreach ($classes as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabClasse[$i]['id'] = $e->getId();
                $tabClasse[$i]['libelle'] = $e->getLibelle();

                $i++;
            }

            $dataService = json_encode($tabClasse); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }

    #[Route(path: '/', name: 'site_home', methods: ['GET', 'POST'])]
    public function index(Request $request, FiliereRepository $filiereRepository): Response
    {

        return $this->render('site/index.html.twig', ['filieres' => $filiereRepository->findAll()]);
    }


    #[Route(path: '/inscription', name: 'site_register')]
    public function inscription_login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        NiveauRepository $niveauRepository,
        PreinscriptionRepository $preinscriptionRepository,
        FormError $formError,
        UtilisateurGroupeRepository $utilisateurGroupeRepository,
        GroupeRepository $groupeRepository,
        FonctionRepository $fonctionRepository,
        UtilisateurRepository $utilisateurRepository,
        SendMailService $sendMailService
        //PreinscriptionRepository $preinscriptionRepository
    ): Response {
        $inscriptionDTO = new InscriptionDTO();
        $value = new DateTime();
        $value->setDate(1900, 1, 1);

        //$inscriptionDTO->setDateNaissance(new \DateTime('01/01/1993'));
        //$inscriptionDTO->setDateNaissance($value());
        $form = $this->createForm(RegisterType::class, $inscriptionDTO, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('site_register'),
        ]);

        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $redirect = $this->generateUrl($loginFormAuthenticator::DEFAULT_INFORMATION);
        $fullRedirect = false;
        if ($form->isSubmitted()) {

            //dd($inscriptionDTO->getDateNaissance());
            $prenoms = '';
            $explodePrenom = explode(" ", $inscriptionDTO->getPrenom());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }
            $response = [];
            $fonction = $entityManager->getRepository(Fonction::class)->findOneByCode('ETD');
            $user = $utilisateurRepository->findOneByEmail($inscriptionDTO->getEmail());
            if ($form->isValid()) {

                if (!$user) {
                    $etudiant = new Etudiant();


                    $etudiant->setNom(strtoupper($inscriptionDTO->getNom()));
                    $etudiant->setPrenom($prenoms);
                    $etudiant->setDateNaissance($inscriptionDTO->getDateNaissance());
                    // $etudiant->setCivilite($inscriptionDTO->getCivilite());
                    $etudiant->setGenre($inscriptionDTO->getGenre());
                    $etudiant->setFonction($fonctionRepository->findOneBy(['code' => 'ETD']));
                    $etudiant->setLieuNaissance('');
                    $etudiant->setEtat('pas_complet');
                    $etudiant->setEmail($inscriptionDTO->getEmail());
                    $etudiant->setContact($inscriptionDTO->getContact());
                    $etudiant->setFonction($fonction);
                    $entityManager->persist($etudiant);

                    $utilisateur = new Utilisateur();
                    $utilisateur->setPassword($userPasswordHasher->hashPassword($utilisateur, $inscriptionDTO->getPlainPassword()));
                    $utilisateur->addRole('ROLE_ETUDIANT');
                    $utilisateur->setEmail($inscriptionDTO->getEmail());
                    $utilisateur->setPersonne($etudiant);
                    $utilisateur->setUsername($inscriptionDTO->getEmail());

                    $entityManager->persist($utilisateur);

                    $entityManager->flush();

                    $groupe = new UtilisateurGroupe();

                    $groupe->setUtilisateur($utilisateur);
                    $groupe->setGroupe($groupeRepository->findOneBy(['libelle' => 'Etudiants']));
                    $utilisateurGroupeRepository->add($groupe, true);

                    $userAuthenticator->authenticateUser(
                        $utilisateur,
                        $loginFormAuthenticator,
                        $request
                    );
                    $preinscription = new Preinscription();
                    $preinscription->setEtat('attente_validation');
                    $preinscription->setEtatDeliberation('pas_deliberer');
                    $preinscription->setEtudiant($etudiant);
                    $preinscription->setDatePreinscription(new \DateTime());
                    $preinscription->setNiveau($inscriptionDTO->getNiveau());
                    $preinscription->setUtilisateur($utilisateur);
                    $preinscription->setCode($this->numero($inscriptionDTO->getNiveau()->getCode()));
                    $preinscriptionRepository->add($preinscription, true);


                    $info_user = [
                        'login' => $inscriptionDTO->getEmail(),
                        'password' => $inscriptionDTO->getPlainPassword()
                    ];

                    $context = compact('info_user');

                    // TO DO
                    $sendMailService->send(
                        'konatenhamed@ufrseg.enig-sarl.com',
                        $inscriptionDTO->getEmail(),
                        'Informations',
                        'content_mail',
                        $context
                    );

                    $statut = 1;
                    $message = 'Compte crée avec succès';
                    $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération vous pouvez consulter votre email');
                } else {

                    $existe = $preinscriptionRepository->findOneBy(['niveau' => $inscriptionDTO->getNiveau()]);
                    if ($existe) {
                        $statut = 1;
                        $message = 'cet étudiant  existe  déjà dans cette filière,veillez vous connecter';
                        $this->addFlash('danger', $message);
                    } else {

                        $preinscription = new Preinscription();
                        $preinscription->setEtat('attente_validation');
                        $preinscription->setEtatDeliberation('pas_deliberer');
                        $preinscription->setEtudiant($user->getPersonne());
                        $preinscription->setDatePreinscription(new \DateTime());
                        $preinscription->setNiveau($inscriptionDTO->getNiveau());
                        $preinscription->setUtilisateur($user);
                        $preinscription->setCode($this->numero($inscriptionDTO->getNiveau()->getCode()));
                        $preinscriptionRepository->add($preinscription, true);
                        $userAuthenticator->authenticateUser(
                            $user,
                            $loginFormAuthenticator,
                            $request
                        );
                    }
                }

                $fullRedirect = true;
                /* $statut = 1;
                $message = 'Compte crée avec succès';
                $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération');*/
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }



            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }


        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
    }

    protected function serialize($data, $format = 'json')
    {
        return $this->container['serializer']->serialize($data, $format);
    }

    #[Route(path: '/site/information', name: 'site_information', methods: ['GET', 'POST'])]
    public function information(
        Request $request,
        UserInterface $user,
        EtudiantRepository $etudiantRepository,
        PersonneRepository $personneRepository,
        FormError $formError,
        NiveauRepository $niveauRepository,
        UtilisateurRepository $utilisateurRepository,
        PreinscriptionRepository $preinscriptionRepository,

    ): Response {
        $etudiant = $etudiantRepository->find($user->getPersonne()->getId());

        $info = new InfoEtudiant();


        //$etudiant->getInf





        if (count($etudiant->getInfoEtudiants()) == 0) {
            $info->setTuteurNomPrenoms('');
            $info->setTuteurFonction('');
            $info->setTuteurContact('');
            $info->setTuteurDomicile('');
            $info->setTuteurEmail('');

            $info->setCorresNomPrenoms('');
            $info->setCorresFonction('');
            $info->setCorresContacts('');
            $info->setCorresDomicile('');
            $info->setCorresEmail('');

            $etudiant->addInfoEtudiant($info);
        }



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantType::class, $etudiant, [
            'method' => 'POST',
            'type' => 'info',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_information')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('site_information');
            $datas = $preinscriptionRepository->findBy(array('etudiant' => $etudiant, 'etat' => 'attente_informations'));

            $prenoms = '';
            $explodePrenom = explode(" ", $form->get('prenom')->getData());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }



            if ($form->isValid()) {
                $etudiant->setNom(strtoupper($form->get('nom')->getData()));
                $etudiant->setPrenom($prenoms);
                if ($form->getClickedButton()->getName() === 'valider') {
                    $etudiant->setEtat('complete');
                    $message       = 'Votre dossier a bien été transmis pour validation. Vous recevrez une notification après traitement.';
                    $etudiantRepository->add($etudiant, true);

                    foreach ($datas as $key => $value) {
                        $value->setEtat('attente_validation');
                        $preinscriptionRepository->add($value, true);
                    }
                } else {
                    $etudiantRepository->add($etudiant, true);
                    $message       = 'Opération effectuée avec succès';
                }



                $data = true;

                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/informations.html.twig', [
            'etudiant' => $etudiant,
            'etat' => 'ok',
            'form' => $form->createView(),
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }

    #[Route(path: '/site/document/{id}', name: 'site_document_autre', methods: ['GET', 'POST'])]
    #[Route(path: '/site/document', name: 'site_document', methods: ['GET', 'POST'])]
    public function document(Request $request, UserInterface $user, PersonneRepository $personneRepository, EtudiantRepository $etudiantRepository, FormError $formError): Response
    {
        $etudiant = $etudiantRepository->find($user->getPersonne()->getId());

        //  dd($user->getPersonne()->getId());

        //$this->getUploadDir(self::UPLOAD_PATH, true);
        /*  } */

        //dd($etudiant);



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(EtudiantDocumentType::class, $etudiant, [
            'method' => 'POST',
            'type' => 'document',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_document')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('site_document');


            if ($form->isValid()) {

                $personneRepository->add($etudiant, true);
                //$entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/document.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }


    #[Route('/inscription/etudiant/admin', name: 'app_inscription_etudiant_admin_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function indexInformationAdmin(Request $request, UserInterface $user, DataTableFactory $dataTableFactory): Response
    {
        $classe = $request->query->get('classe');
        $niveau = $request->query->get('niveau');
        $filiere = $request->query->get('filiere');
        // dd($niveau, $filiere);

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_inscription_etudiant_admin_index', compact('classe', 'niveau', 'filiere')),
        ])->add('classe', EntityType::class, [
            'class' => Classe::class,
            'choice_label' => 'libelle',
            'label' => 'Affichage par classe',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'libelle',
                'label' => 'Niveau',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => 'libelle',
                'label' => 'Filiere',
                // 'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2']
            ]);

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code', 'field' => 'p.code'])
            ->add('nom', TextColumn::class, ['label' => 'Nom', 'field' => 'etudiant.nom'])
            ->add('prenom', TextColumn::class, ['label' => 'Prénoms', 'field' => 'etudiant.prenom'])
            ->add('contact', TextColumn::class, ['label' => 'Contact', 'field' => 'etudiant.contact'])
            ->add('classe', TextColumn::class, ['label' => 'Classe', 'field' => 'classe.libelle'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Inscription::class,
                'query' => function (QueryBuilder $qb) use ($classe, $filiere, $niveau) {
                    $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe'])
                        ->from(Inscription::class, 'p')
                        ->join('p.classe', 'classe')
                        ->join('p.niveau', 'niveau')
                        ->join('niveau.filiere', 'filiere')
                        ->join('p.etudiant', 'etudiant')
                        ->leftJoin('p.caissiere', 'c')
                        ->andWhere('p.classe is not null')
                        ->orderBy('etudiant.nom', 'ASC');

                    //dd($classe, $niveau, $filiere);

                    if ($classe || $niveau || $filiere) {
                        if ($classe) {
                            $qb->andWhere('classe.id = :classe')
                                ->setParameter('classe', $classe);
                        }
                        if ($niveau) {
                            $qb->andWhere('niveau.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }
                        if ($filiere) {
                            $qb->andWhere('filiere.id = :filiere')
                                ->setParameter('filiere', $filiere);
                        }
                    }
                }

            ])
            ->setName('dt_app_inscription_etudiant_admin_' . $classe . '_' . $niveau . '_' . $filiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'new' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];

        $gridId = $classe . '_' . $niveau . '_' . $filiere;
        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'deliberation' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('site_information_edit', ['id' => $context->getEtudiant()->getId()]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'add_echeancier' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_inscription_inscription_edit_admin_echeancier', ['id' =>  $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'label' => "Modifier écheancier",
                                'icon' => '%icon% bi bi-calendar-event',
                                'attrs' => ['class' => 'btn-warning', 'title' => 'Modifier écheancier'],
                                //'render' =>  new ActionRender(fn () => $context->getInfoInscriptions()->count() == 0)
                            ],
                            'new' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('site_information_edit_new', ['id' =>  $context->getEtudiant()->getId()]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-plus-square',
                                'attrs' => ['class' => 'btn-primary', 'title' => 'Nouvelle inscription'],
                                'render' => $renders['new']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]
                        ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('site/admin/index.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }
    #[Route('/liste/inscription/etudiant/admin', name: 'app_liste_inscription_etudiant_admin_index', methods: ['GET', 'POST'])]
    public function indexListeInscris(Request $request, UserInterface $user, DataTableFactory $dataTableFactory): Response
    {


        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code', 'field' => 'p.code'])
            ->add('nom', TextColumn::class, ['label' => 'Nom', 'field' => 'etudiant.nom'])
            ->add('prenom', TextColumn::class, ['label' => 'Prénoms', 'field' => 'etudiant.prenom'])
            ->add('contact', TextColumn::class, ['label' => 'Contact', 'field' => 'etudiant.contact'])
            ->add('classe', TextColumn::class, ['label' => 'Classe', 'field' => 'classe.libelle'])

            ->createAdapter(ORMAdapter::class, [
                'entity' => Etudiant::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'classe'])
                        ->from(Inscription::class, 'p')
                        ->join('p.classe', 'classe')
                        ->join('p.niveau', 'niveau')
                        ->join('niveau.filiere', 'filiere')
                        ->join('p.etudiant', 'etudiant')
                        ->leftJoin('p.caissiere', 'c')
                        ->andWhere('p.classe is not null')
                        ->orderBy('p.id', 'DESC');
                }

            ])
            ->setName('dt_app_liste_inscription_etudiant_admin');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'new' =>  new ActionRender(function () {
                return false;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => []

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('site/admin/index_liste_inscris.html.twig', [
            'datatable' => $table,

        ]);
    }


    #[Route('/all/frais/niveau/{id}', name: 'get_frais', methods: ['GET'])]
    public function getmatiere(Request $request, FraisRepository  $fraisRepository, $id)
    {
        $response = new Response();
        $tabFrais = array();


        // $id = $request->get('id');

        if ($id) {


            $frais = $fraisRepository->findBy(['niveau' => $id]);
            // dd($frais);

            $i = 0;

            foreach ($frais as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabFrais[$i]['id'] = $e->getTypeFrais()->getId();
                $tabFrais[$i]['libelle'] = $e->getTypeFrais()->getLibelle();
                $tabFrais[$i]['montant'] = $e->getMontant();

                $i++;
            }

            $dataService = json_encode($tabFrais); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }


    #[Route(path: '/site/information/new', name: 'site_information_admin_new', methods: ['GET', 'POST'])]
    public function informationAdmin(
        Request $request,
        EtudiantRepository $etudiantRepository,
        PersonneRepository $personneRepository,
        FormError $formError,
        NiveauRepository $niveauRepository,
        PreinscriptionRepository $preinscriptionRepository,
        FonctionRepository $fonctionRepository,
        UtilisateurGroupeRepository $utilisateurGroupeRepository,
        GroupeRepository $groupeRepository,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager,
        SendMailService $sendMailService,
        UserPasswordHasherInterface $userPasswordHasher,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        FraisRepository $fraisRepository,
        Service $service
        // Etudiant $etudiant
    ): Response {

        //dd('');
        $etudiant = new Etudiant();
        $etudiant->setDateNaissance(new DateTime());
        $info = new InfoEtudiant();
        $sommeFrais = 0;
        /*  $frais = $classeRepository->find(2)->getNiveau()->getFrais();
        //dd($frais->count());

        foreach ($frais as $key => $value) {
            $sommeFrais += (int)$value->getMontant();
        } */
        $bloc_echeancier = new BlocEcheancier();

        $bloc_echeancier->setClasse($classeRepository->find(1));

        //$allFrais = $fraisRepository->findBy(['niveau' => $classeRepository->find(3)->getNiveau()]);


        /*   foreach ($allFrais as $key => $value) {
            $fraisBloc = new FraisBloc();
            $fraisBloc->setMontant($value->getMontant());
            $fraisBloc->setTypeFrais($value->getTypeFrais());
            $bloc_echeancier->addFraisBloc($fraisBloc);
        } */


        $bloc_echeancier->setDateInscription(new DateTime());
        //$bloc_echeancier->setTotal($sommeFrais);


        $etudiant->addBlocEcheancier($bloc_echeancier);
        $echeancierProvisoire = new EcheancierProvisoire();
        $echeancierProvisoire->setDateVersement(new DateTime());
        $echeancierProvisoire->setNumero('1');
        $echeancierProvisoire->setMontant('100');

        $bloc_echeancier->addEcheancierProvisoire($echeancierProvisoire);

        if (count($etudiant->getInfoEtudiants()) == 0) {
            $info->setTuteurNomPrenoms('');
            $info->setTuteurFonction('');
            $info->setTuteurContact('');
            $info->setTuteurDomicile('');
            $info->setTuteurEmail('');

            $info->setCorresNomPrenoms('');
            $info->setCorresFonction('');
            $info->setCorresContacts('');
            $info->setCorresDomicile('');
            $info->setCorresEmail('');

            $etudiant->addInfoEtudiant($info);
        }



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantAdminType::class, $etudiant, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_information_admin_new')
        ]);
        $fullRedirect = false;
        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');

            $prenoms = '';
            $explodePrenom = explode(" ", $form->get('prenom')->getData());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }
            $blocEcheanciers = $form->get('blocEcheanciers')->getData();
            // $blocEcheancierssss = $form->get('etudiant_admin_blocEcheanciers_1_autre_fais')->getData();
            //dd($blocEcheancierssss);

            if ($form->isValid()) {


                if (filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $etudiant->setNom(strtoupper($form->get('nom')->getData()));
                    $etudiant->setPrenom($prenoms);
                    $etudiant->setFonction($fonctionRepository->findOneBy(['code' => 'ETD']));
                    $etudiant->setEtat('complete');
                    $entityManager->persist($etudiant);
                    $responseRegister = $service->registerEcheancierAdmin($blocEcheanciers, $etudiant);

                    //dd($responseRegister);
                    if ($responseRegister == true) {
                        //$etudiant->setNom(strtoupper($form->get('nom')->getData()));
                        $entityManager->flush($etudiant);

                        $utilisateur = new Utilisateur();
                        $utilisateur->setPassword($userPasswordHasher->hashPassword($utilisateur, $etudiant->getNom() . '_' . 'password'));
                        $utilisateur->addRole('ROLE_ETUDIANT');
                        $utilisateur->setEmail($etudiant->getEmail());
                        $utilisateur->setPersonne($etudiant);
                        $utilisateur->setUsername($etudiant->getEmail());

                        $utilisateurRepository->add($utilisateur, true);

                        $groupe = new UtilisateurGroupe();

                        $groupe->setUtilisateur($utilisateur);
                        $groupe->setGroupe($groupeRepository->findOneBy(['libelle' => 'Etudiants']));
                        $utilisateurGroupeRepository->add($groupe, true);

                        $info_user = [
                            'login' => $etudiant->getEmail(),
                            'password' => $etudiant->getNom() . '_' . 'password'
                        ];

                        $context = compact('info_user');

                        // TO DO
                        $sendMailService->send(
                            'konatenhamed@ufrseg.enig-sarl.com',
                            $etudiant->getEmail(),
                            'Informations',
                            'content_mail',
                            $context
                        );
                        $statut = 1;
                        $message       = 'Opération effectuée avec succès';
                        $this->addFlash('success', $message);
                    } else {
                        $statut = 0;
                        $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                        $this->addFlash('danger', $message);
                    }





                    /*  $statut = 1;
                    $message       = 'Opération effectuée avec succès';
                    $this->addFlash('success', $message); */
                } else {
                    $statut = 0;
                    $message       = 'Opération échouée car le mail est invalide';
                    $this->addFlash('danger', $message);
                }



                $data = true;
                $fullRedirect = true;
                //$message       = 'Opération effectuée avec succès';
                //$statut = 1;

            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/admin/informations_admin.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
            'frais' => 3,
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }

    #[Route(path: '/site/information/edit/{id}', name: 'site_information_edit', methods: ['GET', 'POST'])]
    public function informationAdminEdit(
        Request $request,
        EtudiantRepository $etudiantRepository,
        PersonneRepository $personneRepository,
        FormError $formError,
        NiveauRepository $niveauRepository,
        UtilisateurRepository $utilisateurRepository,
        PreinscriptionRepository $preinscriptionRepository,
        Etudiant $etudiant,
        SendMailService $sendMailService,
        UserPasswordHasherInterface $userPasswordHasher,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        EntityManagerInterface $entityManager,
        Service $service
    ): Response {




        if (count($etudiant->getBlocEcheanciers()) == 0) {

            /* foreach ($frais as $key => $value) {
            $sommeFrais += (int)$value->getMontant();
        } */
            $bloc_echeancier = new BlocEcheancier();

            $bloc_echeancier->setClasse($classeRepository->find(1));
            $bloc_echeancier->setDateInscription(new DateTime());
            $bloc_echeancier->setTotal('0');


            $etudiant->addBlocEcheancier($bloc_echeancier);
            $echeancierProvisoire = new EcheancierProvisoire();
            $echeancierProvisoire->setDateVersement(new DateTime());
            $echeancierProvisoire->setNumero('1');
            $echeancierProvisoire->setMontant('0');

            $bloc_echeancier->addEcheancierProvisoire($echeancierProvisoire);
        }
        $info = new InfoEtudiant();

        if (count($etudiant->getInfoEtudiants()) == 0) {
            $info->setTuteurNomPrenoms('');
            $info->setTuteurFonction('');
            $info->setTuteurContact('');
            $info->setTuteurDomicile('');
            $info->setTuteurEmail('');

            $info->setCorresNomPrenoms('');
            $info->setCorresFonction('');
            $info->setCorresContacts('');
            $info->setCorresDomicile('');
            $info->setCorresEmail('');

            $etudiant->addInfoEtudiant($info);
        }



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantAdminType::class, $etudiant, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_information_edit', [
                'id' =>  $etudiant->getId()
            ])
        ]);
        $statut = null;
        $data = null;
        $fullRedirect = false;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');

            $user = $utilisateurRepository->findOneBy(['personne' => $etudiant]);
            $blocEcheanciers = $form->get('blocEcheanciers')->getData();
            $prenoms = '';
            $explodePrenom = explode(" ", $form->get('prenom')->getData());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }
            $message = "";
            if ($form->isValid()) {

                //dd(filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL));

                if (filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $etudiant->setNom(strtoupper($form->get('nom')->getData()));
                    $etudiant->setPrenom($prenoms);
                    $entityManager->persist($etudiant);

                    $responseRegister = $service->registerEcheancierAdminEdit($blocEcheanciers, $etudiant);
                    // dd($responseRegister);

                    if ($responseRegister == 'existe') {
                        //il fait rien ici
                        $statut = 0;
                        $message       = "Opération échouée car il existe un inscription similaire à celle que vous envisager de créer actuellement";
                        $this->addFlash('danger', $message);
                    } elseif ($responseRegister == 'pasEgalite') {
                        $statut = 0;
                        $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                        $this->addFlash('danger', $message);
                        // il fait rien ici 
                    } elseif ($responseRegister == 'bonneEgalitePresenceEcheancierPayer') {
                        // bien enregistrer mais l'echeancier n'a pas ete pris en compte car un versment à été deja effectué sur ppur cette inscription

                        $entityManager->flush();

                        $user->setEmail($etudiant->getEmail());
                        $utilisateurRepository->add($user, true);
                        $info_user = [
                            'login' => $etudiant->getEmail(),
                            'password' => $etudiant->getNom() . '_' . 'password'
                        ];

                        $context = compact('info_user');
                        // TO DO
                        $sendMailService->send(
                            'konatenhamed@ufrseg.enig-sarl.com',
                            $etudiant->getEmail(),
                            'Informations',
                            'content_mail',
                            $context
                        );
                        $statut = 1;
                        $message       = "Opération effectuée avec succès mais l'echeancier n'a pas ete pris en compte car un versment à été deja effectué sur pour cette inscription";
                        $this->addFlash('success', $message);
                    } else {

                        $entityManager->flush();

                        $user->setEmail($etudiant->getEmail());
                        $utilisateurRepository->add($user, true);
                        $info_user = [
                            'login' => $etudiant->getEmail(),
                            'password' => $etudiant->getNom() . '_' . 'password'
                        ];

                        $context = compact('info_user');
                        // TO DO
                        $sendMailService->send(
                            'konatenhamed@ufrseg.enig-sarl.com',
                            $etudiant->getEmail(),
                            'Informations',
                            'content_mail',
                            $context
                        );
                        $statut = 1;
                        $message       = "Opération effectuée avec succès";
                        $this->addFlash('success', $message);
                    }


                    $this->addFlash('success', $message);
                } else {
                    $statut = 0;
                    $message       = 'Opération échouée car le mail est invalide';
                    $this->addFlash('danger', $message);
                }

                $data = true;
                $fullRedirect = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/admin/informations_admin_edit.html.twig', [
            'etudiant' => $etudiant,
            'etat' => 'ok',
            'form' => $form->createView(),
            'frais' => 3,
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }
    #[Route(path: '/site/information/new/{id}', name: 'site_information_edit_new', methods: ['GET', 'POST'])]
    public function informationAdminNewEdit(
        Request $request,
        EtudiantRepository $etudiantRepository,
        PersonneRepository $personneRepository,
        FormError $formError,
        NiveauRepository $niveauRepository,
        UtilisateurRepository $utilisateurRepository,
        PreinscriptionRepository $preinscriptionRepository,
        $id,
        SendMailService $sendMailService,
        UserPasswordHasherInterface $userPasswordHasher,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        EntityManagerInterface $entityManager,
        Service $service
    ): Response {

        $etudiant = new Etudiant();


        if (count($etudiant->getBlocEcheanciers()) == 0) {

            /* foreach ($frais as $key => $value) {
            $sommeFrais += (int)$value->getMontant();
        } */
            $bloc_echeancier = new BlocEcheancier();

            $bloc_echeancier->setClasse($classeRepository->find(1));
            $bloc_echeancier->setDateInscription(new DateTime());
            $bloc_echeancier->setTotal('0');


            $etudiant->addBlocEcheancier($bloc_echeancier);
            $echeancierProvisoire = new EcheancierProvisoire();
            $echeancierProvisoire->setDateVersement(new DateTime());
            $echeancierProvisoire->setNumero('1');
            $echeancierProvisoire->setMontant('0');

            $bloc_echeancier->addEcheancierProvisoire($echeancierProvisoire);
        }
        $info = new InfoEtudiant();

        if (count($etudiant->getInfoEtudiants()) == 0) {
            $info->setTuteurNomPrenoms('');
            $info->setTuteurFonction('');
            $info->setTuteurContact('');
            $info->setTuteurDomicile('');
            $info->setTuteurEmail('');

            $info->setCorresNomPrenoms('');
            $info->setCorresFonction('');
            $info->setCorresContacts('');
            $info->setCorresDomicile('');
            $info->setCorresEmail('');

            $etudiant->addInfoEtudiant($info);
        }



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantAdminNewType::class, $etudiant, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_information_edit_new', [
                'id' =>  $id
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');
            $prenoms = '';
            $explodePrenom = explode(" ", $form->get('prenom')->getData());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }

            $user = $utilisateurRepository->findOneBy(['personne' => $etudiant]);
            $blocEcheanciers = $form->get('blocEcheanciers')->getData();

            if ($form->isValid()) {

                //dd(filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL));
                $etudiant->setNom(strtoupper($form->get('nom')->getData()));
                $etudiant->setPrenom($prenoms);
                $responseRegister = $service->registerEcheancierAdmin($blocEcheanciers, $etudiantRepository->find($id));

                if ($responseRegister) {
                    $etudiantRepository->add($etudiantRepository->find($id), true);
                    $statut = 1;
                    $message       = 'Opération effectuée avec succès';
                    $this->addFlash('success', $message);
                } else {
                    $statut = 0;
                    $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                    $this->addFlash('danger', $message);
                }


                $data = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/admin/new.html.twig', [
            'etudiant' => $etudiant,
            'etat' => 'ok',
            'form' => $form->createView(),
            'frais' => 3,
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }
    #[Route(path: '/site/information/validation/direct/after/demande/{id}', name: 'site_information_validation_direct_after_demande', methods: ['GET', 'POST'])]
    public function informationAdminNewDirectAfterDemandeEtudiant(
        Request $request,
        EtudiantRepository $etudiantRepository,
        PersonneRepository $personneRepository,
        FormError $formError,
        NiveauRepository $niveauRepository,
        UtilisateurRepository $utilisateurRepository,
        PreinscriptionRepository $preinscriptionRepository,
        $id,
        SendMailService $sendMailService,
        UserPasswordHasherInterface $userPasswordHasher,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        EcheancierRepository $echeancierRepository,
        EntityManagerInterface $entityManager,
        Service $service
    ): Response {

        $etudiant = new Etudiant();


        if (count($etudiant->getBlocEcheanciers()) == 0) {

            /* foreach ($frais as $key => $value) {
            $sommeFrais += (int)$value->getMontant();
        } */
            $bloc_echeancier = new BlocEcheancier();

            $bloc_echeancier->setClasse($classeRepository->find(1));
            $bloc_echeancier->setDateInscription(new DateTime());
            $bloc_echeancier->setTotal('0');


            $etudiant->addBlocEcheancier($bloc_echeancier);
            $echeancierProvisoire = new EcheancierProvisoire();
            $echeancierProvisoire->setDateVersement(new DateTime());
            $echeancierProvisoire->setNumero('1');
            $echeancierProvisoire->setMontant('0');

            $bloc_echeancier->addEcheancierProvisoire($echeancierProvisoire);
        }
        $info = new InfoEtudiant();

        if (count($etudiant->getInfoEtudiants()) == 0) {
            $info->setTuteurNomPrenoms('');
            $info->setTuteurFonction('');
            $info->setTuteurContact('');
            $info->setTuteurDomicile('');
            $info->setTuteurEmail('');

            $info->setCorresNomPrenoms('');
            $info->setCorresFonction('');
            $info->setCorresContacts('');
            $info->setCorresDomicile('');
            $info->setCorresEmail('');

            $etudiant->addInfoEtudiant($info);
        }



        $validationGroups = ['Default', 'FileRequired', 'autre'];
        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantAdminNewType::class, $etudiant, [
            'method' => 'POST',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_information_validation_direct_after_demande', [
                'id' =>  $id,

            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');

            $user = $utilisateurRepository->findOneBy(['personne' => $etudiant]);
            $blocEcheanciers = $form->get('blocEcheanciers')->getData();

            $prenoms = '';
            $explodePrenom = explode(" ", $form->get('prenom')->getData());
            for ($i = 0; $i < count($explodePrenom); $i++) {
                $prenoms = $prenoms . ' ' . ucfirst($explodePrenom[$i]);
            }

            if ($form->isValid()) {

                //dd(filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL));
                $etudiant->setNom(strtoupper($form->get('nom')->getData()));
                $etudiant->setPrenom($prenoms);
                $responseRegister = $service->registerEcheancierAdmin($blocEcheanciers, $etudiantRepository->find($id));

                if ($responseRegister) {
                    $etudiantRepository->add($etudiantRepository->find($id), true);
                    $statut = 1;
                    $message       = 'Opération effectuée avec succès';
                    $this->addFlash('success', $message);
                } else {
                    $statut = 0;
                    $message       = "Opération échouée car le montant total à payer est different du montant total de l 'échanece";
                    $this->addFlash('danger', $message);
                }


                $data = true;
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('site/admin/new_validation.html.twig', [
            'etudiant' => $etudiant,
            'etat' => 'ok',
            'form' => $form->createView(),
            'frais' => 3,
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }


    #[Route('/site/admin/paiement/admin/ok', name: 'app_inscription_inscription_site_admin_paiement_ok', methods: ['GET', 'POST'])]
    public function paiement(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository, EcheancierRepository $echeancierRepository, UserInterface $user, NaturePaiementRepository $naturePaiementRepository): Response
    {



        $form = $this->createForm(InscriptionPayementType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_site_admin_paiement_ok')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_etudiant_admin_index');
            //$ligne = $form->get('echeanciers')->getData();

            // $workflow_data = $this->workflow->get($inscription, 'inscription');

            $echeanciers = $echeancierRepository->findAllEcheance($inscription->getId());
            $date = $form->get('datePaiement')->getData();
            $mode =   $mode = $naturePaiementRepository->find($form->get('modePaiement')->getData()->getId());

            $montant = (int) $form->get('montant')->getData();

            //dd($inscription->getId());



            if ($form->isValid()) {

                $last_key = count($echeanciers);
                $i = 1;
                foreach ($echeanciers as $key => $echeancier) {

                    //  dd($montant, $echeancier->getMontant());
                    if ($montant >= (int)$echeancier->getMontant()) {

                        $paiement = new InfoInscription();

                        $paiement->setUtilisateur($this->getUser());
                        $paiement->setCode($inscription->getCode());
                        $paiement->setDateValidation(new \DateTime());
                        $paiement->setInscription($inscription);
                        $paiement->setDatePaiement($date);
                        $paiement->setCaissiere($this->getUser());
                        $paiement->setModePaiement($mode);
                        $paiement->setMontant($echeancier->getMontant());
                        /// $paiement->setEchenacier($echeancier);
                        if ($mode->getCode() == 'CHQ') {

                            $paiement->setNumeroCheque($form->get('numeroCheque')->getData());
                            $paiement->setBanque($form->get('banque')->getData());
                            $paiement->setTireur($form->get('tireur')->getData());
                            $paiement->setContact($form->get('contact')->getData());
                            $paiement->setDateCheque($form->get('dateCheque')->getData());
                        }


                        if ($mode->isConfirmation()) {

                            $paiement->setEtat('attente_confirmation');
                        } else {

                            $paiement->setEtat('payer');
                        }


                        $entityManager->persist($paiement);
                        $entityManager->flush();

                        if ($mode->isConfirmation()) {

                            $echeancier->setEtat('attente_confirmation');
                        } else {

                            $echeancier->setEtat('payer');
                        }
                        $entityManager->persist($echeancier);
                        $entityManager->flush();



                        $montant = $montant - $echeancier->getMontant();
                        if (!$mode->isConfirmation()) {

                            $inscription->setTotalPaye($inscription->getTotalPaye() + $echeancier->getMontant());
                        }

                        $entityManager->persist($inscription);
                        $entityManager->flush();

                        if ($i == $last_key) {

                            if ($montant >= 0) {

                                if ($inscription->getMontant() == $inscription->getTotalPaye()) {

                                    $inscription->setEtat('solde');
                                }
                            }
                        }

                        //$message       = sprintf('Opération effectuée avec succès');
                    }

                    $i++;
                }
                $message       = sprintf('Opération effectuée avec succès');
                if ($inscription->getMontant() == $inscription->getTotalPaye()) {
                    $statut = 1;
                } else {
                    $statut = 0;
                    $this->addFlash('success', $message);
                }

                $showAlert = true;
                $data = true;


                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/edit_paiement.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'echeanciers' => $echeancierRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }
}
