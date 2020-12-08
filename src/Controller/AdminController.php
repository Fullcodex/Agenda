<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Personne;
use App\Entity\Acceder;
use App\Entity\Agenda;
use App\Entity\Evenement;
use App\Entity\Categorie;
use App\Form\AdminUserFormType;
use App\Form\AdminAccesFormType;
use App\Form\AdminAgendaFormType;
use App\Form\AdminCategorieFormType;
use DateInterval;
use DatePeriod;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController {

    /**
     * @Route("/admin", name="accueil_admin")
     */
    public function index(): Response {
        return $this->render('admin/index.html.twig', [
                    'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/user", name="user")
     */
    public function gestionUser(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd

        if (!empty($_POST)) {
            $unUser = new Personne();

            $formRequest = $this->createForm(AdminUserFormType::class, $unUser);
            $formRequest->handleRequest($request);

            if ($formRequest->isSubmitted()) {

                if (isset($_POST['updateUser'])) {
                    $User = $bdd->getRepository(Personne::class)->find($formRequest->getData()->getid());
                    $User->setEmail($formRequest->getData()->getEmail());
                    $User->setNompersonnes($formRequest->getData()->getNompersonnes());
                    $User->setDatenaissance($formRequest->getData()->getDatenaissance());
//                    if (!empty($formRequest->getData()->getPassword())) {
//                        $User->setPassword(
//                                $passwordEncoder->encodePassword($User, $formRequest->getData()->getPassword())
//                        );
//                    }
                    $bdd->flush();
                }
//                elseif (isset($_POST['insertUser']) && $formRequest->isValid()) {
//                    $bdd->persist($User);
//                    $User->setPassword(
//                            $passwordEncoder->encodePassword($User, $formRequest->getData()->getPassword())
//                    );
//                    $bdd->flush();
//                }
                elseif (isset($_POST['deleteUser'])) {
                    $User = $bdd->getRepository(Personne::class)->find($formRequest->getData()->getid());
                    $bdd->remove($User);
                    $bdd->flush();
                }
            }
        }

        if (isset($_POST['searchEmail'])) {
            $query = $bdd->createQuery('SELECT P FROM App\Entity\Personne P WHERE P.email LIKE :Email'); //Recherche des droit
            $query->setParameter(':Email', '%' . $_POST['searchEmail'] . '%');
            $AllUser = $query->getResult();
        }
        else {
            $AllUser = $bdd->getRepository(Personne::class)->findAll();
        }

        foreach ($AllUser as $unUser) {
            $formMaj[] = $this->createForm(AdminUserFormType::class, $unUser)->createView();
        }


        $formInsert = $this->createForm(AdminUserFormType::class);

        return $this->render('admin/gestionUser.html.twig', [
                    'AllUser'        => $AllUser,
                    'formUserInsert' => $formInsert->createView(),
                    'formUserMaj'    => $formMaj
        ]);
    }

    /**
     * @Route("/admin/acces", name="acces")
     */
    public function gestionAcces(Request $request): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd

        if (!empty($_POST)) {
            $LAcces = new Acceder();
            $formRequest = $this->createForm(AdminAccesFormType::class, $LAcces);
            $formRequest->handleRequest($request);


            if ($formRequest->isSubmitted()) {

                if (isset($_POST['insertAcces'])) {
                    $Acces = new Acceder();
                    $Acces->setRefPersonne($formRequest->getData()->getRefPersonne());
                    $Acces->setAgendas($formRequest->getData()->getAgendas());
                    $Acces->setIdDroit($formRequest->getData()->getIdDroit());
                    $bdd->persist($Acces);
                    $bdd->flush();
                }
                elseif (isset($_POST['updateAcces'])) {
                    $Acces = $bdd->getRepository(Acceder::class)->find($formRequest->getData()->getId());
                    $Acces->setRefPersonne($formRequest->getData()->getRefPersonne());
                    $Acces->setAgendas($formRequest->getData()->getAgendas());
                    $Acces->setIdDroit($formRequest->getData()->getIdDroit());
                    $bdd->flush();
                }
                elseif (isset($_POST['deleteAcces'])) {
                    $Acces = $bdd->getRepository(Acceder::class)->find($formRequest->getData()->getId());
                    $bdd->remove($Acces);
                    $bdd->flush();
                }
            }
        }

        if (isset($_POST['searchEmail']) || isset($_POST['searchAgenda'])) {
            if (!empty($_POST['searchEmail']) && $_POST['searchAgenda'] == 'Choisir') {
                $filtre = ' WHERE P.email LIKE :Email';
            }
            elseif (empty($_POST['searchEmail']) && $_POST['searchAgenda'] !== 'Choisir') {
                $filtre = ' WHERE A.id = :Agenda';
            }
            elseif (!empty($_POST['searchEmail']) && $_POST['searchAgenda'] !== 'Choisir') {
                $filtre = ' WHERE P.email LIKE :Email AND A.id = :Agenda';
            }
            else {
                $filtre = '';
            }

            $query = $bdd->createQuery('SELECT Acc FROM App\Entity\Acceder Acc JOIN Acc.Ref_Personne P JOIN Acc.Ref_Agendas A ' . $filtre); //Recherche des droit

            if (!empty($_POST['searchEmail']) && $_POST['searchAgenda'] == 'Choisir') {
                $query->setParameter(':Email', '%' . $_POST['searchEmail'] . '%');
            }
            elseif (empty($_POST['searchEmail']) && $_POST['searchAgenda'] !== 'Choisir') {
                $query->setParameter(':Agenda', $_POST['searchAgenda']);
            }
            elseif (!empty($_POST['searchEmail']) && $_POST['searchAgenda'] !== 'Choisir') {
                $query->setParameters(array(':Agenda' => $_POST['searchAgenda'], ':Email' => '%' . $_POST['searchEmail'] . '%'));
            }

            $AllAcces = $query->getResult();
        }
        else {
            $AllAcces = $bdd->getRepository(Acceder::class)->findAll();
        }


        foreach ($AllAcces as $unAcces) {
            $formMaj[] = $this->createForm(AdminAccesFormType::class, $unAcces)->createView();
        }

        $AllAgenda = $bdd->getRepository(Agenda::class)->findAll();
        $LAcces = new Acceder();
        $formInsert = $this->createForm(AdminAccesFormType::class, $LAcces);


        return $this->render('admin/gestionAcces.html.twig', [
                    'AllAcces'        => $AllAcces,
                    'AllAgenda'       => $AllAgenda,
                    'formAccesInsert' => $formInsert->createView(),
                    'formAccesMaj'    => $formMaj
        ]);
    }

    /**
     * @Route("/admin/agenda", name="agenda")
     */
    public function gestionAgenda(Request $request): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd


        if (!empty($_POST)) {
            $LeAgenda = new Agenda();
            $formRequest = $this->createForm(AdminAgendaFormType::class, $LeAgenda);
            $formRequest->handleRequest($request);

            if ($formRequest->isSubmitted()) {

                if (isset($_POST['insertAgenda'])) {
                    $Agenda = new Agenda();
                    $Agenda->setNom($formRequest->getData()->getNom());
                    $bdd->persist($Agenda);
                    $bdd->flush();
                }
                elseif (isset($_POST['updateAgenda'])) {
                    $Agenda = $bdd->getRepository(Agenda::class)->find($formRequest->getData()->getId());
                    $Agenda->setNom($formRequest->getData()->getNom());
                    $bdd->flush();
                }
                elseif (isset($_POST['deleteAgenda'])) {
                    $Agenda = $bdd->getRepository(Agenda::class)->find($formRequest->getData()->getId());
                    $bdd->remove($Agenda);
                    $bdd->flush();
                }
                elseif (isset($_POST['generateKeyAgenda'])) {
                    $Agenda = $bdd->getRepository(Agenda::class)->find($formRequest->getData()->getId());
                    $Agenda->setCle(uniqid($Agenda->getId()));
                    $Agenda->setDtCle(date_create());
                    $bdd->flush();
                }
            }
        }

        if (isset($_POST['searchAgenda'])) {
            $query = $bdd->createQuery('SELECT A FROM App\Entity\Agenda A WHERE A.id = :Agenda '); //Recherche des droit
            $query->setParameter(':Agenda', $_POST['searchAgenda']);
            $AllAgendas = $query->getResult();
        }
        else {
            $AllAgendas = $bdd->getRepository(Agenda::class)->findAll();
        }


        foreach ($AllAgendas as $unAgenda) {
            $formMaj[] = $this->createForm(AdminAgendaFormType::class, $unAgenda)->createView();
        }

        $AllAgendasFilter = $bdd->getRepository(Agenda::class)->findAll();
        $LeAgenda = new Agenda();
        $formInsert = $this->createForm(AdminAgendaFormType::class, $LeAgenda);


        return $this->render('admin/gestionAgenda.html.twig', [
                    'AllAgendas'       => $AllAgendas,
                    'AllAgendaFilter'  => $AllAgendasFilter,
                    'formAgendaInsert' => $formInsert->createView(),
                    'formAgendaMaj'    => $formMaj
        ]);
    }

    /**
     * @Route("/admin/evenement", name="evenement")
     */
    public function gestionEvenement(Request $request): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd


        if (!empty($_POST)) {

            if (isset($_POST['enregistrerEvenement'])) {
                $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['selectAgendaForm']);
                $Categorie = $bdd->getRepository(Categorie::class)->find($_POST['selectCategorie']);
                if (!empty($_POST['inputRecurrence']) && !empty($_POST['typeRecurrence']) && !empty($_POST['finRecurrence'])) {
                    $Interval = new DateInterval('P' . $_POST['inputRecurrence'] . $_POST['typeRecurrence']);
                    $DateRange = new DatePeriod(date_create($_POST['dtDebut']), $Interval, date_create($_POST['finRecurrence'] . ' +1 day'));

                    foreach ($DateRange as $uneDateEvent) {
                        $Event = new Evenement();
                        $Event->setLibelle($_POST['inputLibelle']);
                        $Event->setNote($_POST['inputNote']);
                        $Event->setLieu($_POST['inputLieu']);
                        $Event->setCouleur($_POST['selectCouleur']);
                        $Event->setDateDebut(date_create($uneDateEvent->format('Y-m-d ') . $_POST['timeDebut']));
                        $Event->setDateFin(date_create($uneDateEvent->format('Y-m-d ') . $_POST['timeFin']));
                        $Event->setAgenda($Agenda);
                        $Event->setCategorie($Categorie);
                        $bdd->persist($Event);
                    }
                }
                else {
                    $Event = new Evenement();
                    $Event->setLibelle($_POST['inputLibelle']);
                    $Event->setNote($_POST['inputNote']);
                    $Event->setLieu($_POST['inputLieu']);
                    $Event->setCouleur($_POST['selectCouleur']);
                    $Event->setDateDebut(date_create($_POST['dtDebut'] . $_POST['timeDebut']));
                    $Event->setDateFin(date_create($_POST['dtDebut'] . $_POST['timeFin']));
                    $Event->setAgenda($Agenda);
                    $Event->setCategorie($Categorie);
                    $bdd->persist($Event);
                }

                $bdd->flush(); //Enregistre les changements dans la bdd
            }
            elseif (isset($_POST['updateEvenement'])) {
                $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['selectAgendaForm']);
                $Categorie = $bdd->getRepository(Categorie::class)->find($_POST['selectCategorie']);
                $Event = $bdd->getRepository(Evenement::class)->find($_POST['updateEvenement']);
                if (isset($_POST['checkIntervale'])) {

                    $Interval = date_diff($Event->getDateDebut(), date_create($_POST['dtDebut'] . $_POST['timeDebut']));

                    $AllEvent = $bdd->getRepository(Evenement::class)->findBy(array(
                        'Agenda'    => $Event->getAgenda(),
                        'Categorie' => $Event->getCategorie(),
                        'Libelle'   => $Event->getLibelle(),
                        'Note'      => $Event->getNote(),
                        'Lieu'      => $Event->getLieu()
                    ));
                    foreach ($AllEvent as $unEvent) {
                        $unEvent->setLibelle($_POST['inputLibelle']);
                        $unEvent->setNote($_POST['inputNote']);
                        $unEvent->setLieu($_POST['inputLieu']);
                        $unEvent->setCouleur($_POST['selectCouleur']);
                        $unEvent->setDateDebut($unEvent->getDateDebut()->add($Interval));
                        $unEvent->setDateFin($unEvent->getDateFin()->add($Interval));
                        $unEvent->setAgenda($Agenda);
                        $unEvent->setCategorie($Categorie);
                        $bdd->persist($Event);
                    }
                }
                else {
                    $Event->setLibelle($_POST['inputLibelle']);
                    $Event->setNote($_POST['inputNote']);
                    $Event->setLieu($_POST['inputLieu']);
                    $Event->setCouleur($_POST['selectCouleur']);
                    $Event->setDateDebut(date_create($_POST['dtDebut'] . $_POST['timeDebut']));
                    $Event->setDateFin(date_create($_POST['dtDebut'] . $_POST['timeFin']));
                    $Event->setAgenda($Agenda);
                    $Event->setCategorie($Categorie);
                    $bdd->persist($Event);
                }

                $bdd->flush(); //Enregistre les changements dans la bdd
            }
            elseif (isset($_POST['deleteEvenement'])) {
                $Event = $bdd->getRepository(Evenement::class)->find($_POST['deleteEvenement']);

                if (isset($_POST['checkIntervale'])) {

                    $Interval = date_diff($Event->getDateDebut(), date_create($_POST['dtDebut'] . $_POST['timeDebut']));

                    $AllEvent = $bdd->getRepository(Evenement::class)->findBy(array(
                        'Agenda'    => $Event->getAgenda(),
                        'Categorie' => $Event->getCategorie(),
                        'Libelle'   => $Event->getLibelle(),
                        'Note'      => $Event->getNote(),
                        'Lieu'      => $Event->getLieu()
                    ));
                    foreach ($AllEvent as $unEvent) {
                        $bdd->remove($unEvent);
                        $bdd->flush();
                    }
                }
                else {
                    $bdd->remove($Event);
                    $bdd->flush();
                }
            }
        }

        if (isset($_POST['searchAgenda'])) {
            $query = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE E.Agenda = :Agenda '); //Recherche des droit
            $query->setParameter(':Agenda', $_POST['searchAgenda']);
            $AllEvent = $query->getResult();
        }
        else {
            $AllEvent = $bdd->getRepository(Evenement::class)->findAll();
        }

        $AllAgendasFilter = $bdd->getRepository(Agenda::class)->findAll();
        $AllCategorie = $bdd->getRepository(Categorie::class)->findAll();


        return $this->render('admin/gestionEvenement.html.twig', [
                    'AllEvenement'    => $AllEvent,
                    'AllCategorie'    => $AllCategorie,
                    'AllAgendaFilter' => $AllAgendasFilter,
        ]);
    }

    /**
     * @Route("/admin/categorie", name="categorie")
     */
    public function gestionCategorie(Request $request): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd


        if (!empty($_POST)) {
            $LaCategorie = new Categorie();
            $formRequest = $this->createForm(AdminCategorieFormType::class, $LaCategorie);
            $formRequest->handleRequest($request);

            if ($formRequest->isSubmitted()) {

                if (isset($_POST['insertCategorie'])) {
                    $Categorie = new Categorie();
                    $Categorie->setLibelle($formRequest->getData()->getLibelle());
                    $bdd->persist($Categorie);
                    $bdd->flush();
                }
                elseif (isset($_POST['updateCategorie'])) {
                    $Categorie = $bdd->getRepository(Categorie::class)->find($formRequest->getData()->getId());
                    $Categorie->setLibelle($formRequest->getData()->getLibelle());
                    $bdd->flush();
                }
                elseif (isset($_POST['deleteCategorie'])) {
                    $Categorie = $bdd->getRepository(Categorie::class)->find($formRequest->getData()->getId());
                    $bdd->remove($Categorie);
                    $bdd->flush();
                }
            }
        }


        $AllCategorie = $bdd->getRepository(Categorie::class)->findAll();


        foreach ($AllCategorie as $uneCategorie) {
            $formMaj[] = $this->createForm(AdminCategorieFormType::class, $uneCategorie)->createView();
        }

        $LaCategorie = new Categorie();
        $formInsert = $this->createForm(AdminCategorieFormType::class, $LaCategorie);


        return $this->render('admin/gestionCategorie.html.twig', [
                    'formCategorieInsert' => $formInsert->createView(),
                    'formCategorieMaj'    => $formMaj
        ]);
    }

}
