<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swift_Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Personne;
use App\Entity\Acceder;
use App\Entity\Agenda;
use App\Entity\Evenement;
use App\Entity\Pj;
use App\Entity\Categorie;
use App\Entity\Droit;
use App\classPerso\DatePerso;
use DateTime;
use DateInterval;
use DatePeriod;
use function dump;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class AccueilController extends AbstractController {

    /**
     * Route par default qui affiche l'agenda
     * @Route("/accueil/{uneDate}/{idAgendaSelect}", name="accueil", defaults={"uneDate"=null, "idAgendaSelect"=null})
     */
    public function index($uneDate, $idAgendaSelect): Response {
        $Date = new DatePerso();

        $selectedAgenda = null;
        $allAcces = array();
        $allEvents = array();
        $tabGant = array();
        $myAcces = false;

        //Connexion base de donnée
        $bdd = $this->getDoctrine()->getManager();

        //Recupération de l'utilisateur connecté
        $User = $this->getUser();

        //Recuperation des agendas appartir de l'id Personne
        $query1 = $bdd->createQuery('SELECT Acc, A FROM App\Entity\Acceder Acc JOIN Acc.Ref_Agendas A WHERE Acc.Ref_Personne = :Personne');
        $query1->setParameter(':Personne', $User);
        $allAgendas = $query1->getResult();

        //Recuperation des droits
        $query2 = $bdd->createQuery('SELECT D FROM App\Entity\Droit D');
        $allDroit = $query2->getResult();

        //Recuperation des categories
        $query3 = $bdd->createQuery('SELECT C FROM App\Entity\Categorie C');
        $allCategorie = $query3->getResult();

        //Gestion du filtre agenda
        if (isset($_POST['selectAgenda']) || $idAgendaSelect !== null) {
            $idAgendaSelect = $_POST['selectAgenda'] ?? $idAgendaSelect;
            if ($idAgendaSelect !== 'all') {
                foreach ($allAgendas as $unAgenda) {
                    if ($unAgenda->getAgendas()->getId() == $idAgendaSelect) {
                        $selectedAgenda = $unAgenda->getAgendas();

                        //Récupération des evenements par agenda
                        $query3 = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE A.id = :Agenda ORDER BY E.Libelle');
                        $query3->setParameter(':Agenda', $selectedAgenda);
                        $allEvents[] = $query3->getResult();

                        //Récupération des acces, des personnes, des droits par gendas
                        $query4 = $bdd->createQuery('SELECT Acc, P, D FROM App\Entity\Acceder Acc JOIN Acc.Ref_Agendas A JOIN Acc.Ref_Personne P JOIN Acc.Id_Droit D WHERE Acc.Ref_Agendas = :Agenda');
                        $query4->setParameter(':Agenda', $selectedAgenda);
                        $allAcces = $query4->getResult();
                        break;
                    }
                }

                //Gestion des accès en fonction de l'agenda selectionné
                $myAcces = false;
                foreach ($allAcces as $unAcces) {
                    if ($unAcces->getRefPersonne()->getId() == $User->getId()) {
                        if ($unAcces->getIdDroit()->getId() == 1) {
                            $myAcces = true;
                        }
                    }
                }
            }
            else {
                //Récupération des evenements par agenda
                foreach ($allAgendas as $unAgenda) {
                    $query4 = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE A.id = :Agenda ORDER BY E.Libelle');
                    $query4->setParameter(':Agenda', $unAgenda->getAgendas());
                    $allEvents[] = $query4->getResult();
                }
            }
        }
        else {
            //Récupération des evenements par agenda
            foreach ($allAgendas as $unAgenda) {
                $query4 = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE A.id = :Agenda ORDER BY E.Libelle');
                $query4->setParameter(':Agenda', $unAgenda->getAgendas());
                $allEvents[] = $query4->getResult();
            }
        }

//        dump($User);
//        dump($allAgendas);
//        dump($unAgenda);
//        dump($allEvents);

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        if (($_POST['selectAffichage'] ?? 0) == 1) {
            foreach ($Date->getDateRage() as $dateParcour) {
                $tabGant['date'][] = $dateParcour->format('d');
            }

            foreach ($Date->getDateRage() as $dateParcour) {
                foreach ($allEvents as $unAgenda) {
                    foreach ($unAgenda as $unEvent) {
                        if ($unEvent->getDateDebut()->format('Y-m-d') == $dateParcour->format('Y-m-d')) {
                            $tabGant[$unEvent->getLibelle()][$dateParcour->format('Y-m-d')] = $unEvent;
                        }
                    }
                }
            }

            foreach ($Date->getDateRage() as $dateParcour) {
                foreach ($tabGant as $keyLigne => $uneLigne) {
                    if ($keyLigne !== 'date') {
                        if (!isset($uneLigne[$dateParcour->format('Y-m-d')])) {
                            $tabGant[$keyLigne][$dateParcour->format('Y-m-d')] = null;
                        }
                        ksort($tabGant[$keyLigne]);
                    }
                }
            }
        }

        return $this->render('accueil/index.html.twig', [
                    'User'           => $User,
                    'Date'           => $Date,
                    'allAgendas'     => $allAgendas,
                    'allDroits'      => $allDroit,
                    'allCategorie'   => $allCategorie,
                    'allAcces'       => $allAcces,
                    'allEvents'      => $allEvents,
                    'selectedAgenda' => $selectedAgenda,
                    'myAcces'        => $myAcces,
                    'affichage'      => $_POST['selectAffichage'] ?? 0,
                    'tabGantt'       => $tabGant
        ]);
    }

    /**
     * Route de mise a jour des utilisateurs
     * @Route("/updateUser/{uneDate}/{id}", name="updateUser", defaults={"uneDate"=null})
     */
    public function updateUser($uneDate, Personne $User, LoginFormAuthenticator $authenticator): Response {

        $Date = new DatePerso();

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        $User->setNompersonnes($_POST['inputNomUser']);
        $User->setEmail($_POST['inputEmailUser']);
        $User->setDatenaissance(date_create($_POST['inputDtNaissUser']));

        //Gestion de la modification du mot de passe
        if (!empty($_POST['inputPwd1']) && !empty($_POST['inputPwd2'])) {
            if ($_POST['inputPwd1'] == $_POST['inputPwd2']) {
                $mdp = $authenticator->encoderMdp($User, $_POST['inputPwd2']);
                $User->setMdp($mdp);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * Route de d'insertion d'un agenda
     * @Route("/insertAgenda/{uneDate}/{id}", name="insertAgenda", defaults={"uneDate"=null})
     */
    public function insertAgenda($uneDate, Personne $User): Response {

        $Date = new DatePerso();
        $Agenda = new Agenda();
        $Acces = new Acceder();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd

        $query = $bdd->createQuery('SELECT d FROM App\Entity\Droit d WHERE d.id = 1'); //Recherche du droit Lecture|Ecriture
        $Droit = $query->getResult();

        //Modification des information agenda
        $Agenda->setNom($_POST['inputNom']);
        $Agenda->setImg($_FILES['fileImg']['name']);

        //Modification des informations d'acces
        $Acces->setRefPersonne($User);
        $Acces->setIdDroit($Droit[0]);

        $bdd->persist($Agenda); //Modifie les changements dans la bdd
        $bdd->persist($Acces); //Modifie les changements dans la bdd

        $Acces->setAgendas($Agenda);

        $bdd->flush(); //Enregistre les changements dans la bdd

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * Route de mise a jour de l'agenda
     * @Route("/updateAgenda/{uneDate}/{id}", name="updateAgenda", defaults={"uneDate"=null})
     */
    public function updateAgenda($uneDate, Agenda $unAgenda): Response {

        $Date = new DatePerso();

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        $unAgenda->setNom($_POST['inputNom']);
        if (!empty($_POST['fileImg'])) {
            $unAgenda->setImg($_POST['fileImg']);
        }

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * Rout de supressions des agendas
     * @Route("/delAgenda/{uneDate}/{id}", name="delAgenda", defaults={"uneDate"=null})
     */
    public function delAgenda($uneDate, Agenda $unAgenda): Response {

        $Date = new DatePerso();

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        $bdd = $this->getDoctrine()->getManager();

        $query4 = $bdd->createQuery('SELECT Acc FROM App\Entity\Acceder Acc WHERE Acc.Ref_Agendas = :Agenda'); //Recherche des droit
        $query4->setParameter(':Agenda', $unAgenda);
        $allAcces = $query4->getResult();

        foreach ($allAcces as $unAcces) {
            if ($unAcces->getAgendas()->getId() == $unAgenda->getId()) {
                $bdd->remove($unAcces);
            }
        }

        $bdd->remove($unAgenda);
        $bdd->flush();
        return $this->redirectToRoute('accueil');
    }

    /**
     * Route pour le partage d'agenda
     * @Route("/shareAgenda", name="shareAgenda", defaults={"uneDate"=null})
     */
    public function shareAgenda(Swift_Mailer $mailer): Response {

        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $jsonData = array();

        //Verifie si on recupère le mail, l'expediteur et l'agenda de la requete POST
        if (isset($_POST['mail']) && isset($_POST['expediteur']) && isset($_POST['agenda'])) {
            //Récupére l'expediteur a partir de l'id
            $Expediteur = $bdd->getRepository(Personne::class)->find($_POST['expediteur']);
            //Recupère le destinataire a partir de l'email 
            $Receveur = $bdd->getRepository(Personne::class)->findOneBy(array('email' => $_POST['mail']));
            //Recupère l'agenda
            $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['agenda']);
            $Interval = new DateInterval('P1D');

            //Si le destinatair n'est pas vide
            if (!empty($Receveur)) {
                //Si on a un clé et qu'elle à été généré a plus d'un jour on génère une nouvelle
                if (empty($Agenda->getCle()) || ($Agenda->getDtCle()->add($Interval) >= date_create())) {
                    $Agenda->setCle(uniqid($Agenda->getId()));
                    $Agenda->setDtCle(date_create());
                    $bdd->persist($Agenda);
                    $bdd->flush();
                }
                $Cle = $Agenda->getCle();
                //Création et envoie du mail
                if (!empty($Cle)) {
                    $link = 'http://localhost:8000/confirmeAgenda/' . $Receveur->getId() . '/' . $Cle;

                    $to = $Receveur->getEmail();
                    $subject = 'Partage de l\'agenda';

                    $message = (new \Swift_Message($subject))
                            ->setFrom('noreply@angenda.com')
                            ->setTo($to)
                            ->setBody(
                            $this->renderView(
                                    'mail/mail.html.twig',
                                    [
                                        'expediteur' => empty($Expediteur->getNomPrenom()) ? $Expediteur->getEmail() : $Expediteur->getNomPrenom(),
                                        'link'       => $link
                                    ]
                            )
                    );

                    $mailer->send($message);

                    //Retour de confirmation JSON pour confirmer l'envoie
                    $jsonData = array('Nom_Personnes' => empty($Receveur->getNomPrenom()) ? $Receveur->getEmail() : $Receveur->getNomPrenom());
                }
            }
        }

        return new JsonResponse($jsonData);
    }

    /**
     * Route pour geré la confirmation d'acces a un agenda
     * @Route("/confirmeAgenda/{id}/{key}", name="confirmeAgenda", defaults={"key"=null})
     */
    public function confirmeAgenda(Personne $Personne, $key): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd      

        $Agenda = $bdd->getRepository(Agenda::class)->findOneBy(array('Cle_Partage' => $key));
        $Droit = $bdd->getRepository(Droit::class)->find(0);


        if (!empty($Personne) && !empty($Agenda)) {
            $Acces = new Acceder();
            $Acces->setAgendas($Agenda);
            $Acces->setRefPersonne($Personne);
            $Acces->setIdDroit($Droit);
            $bdd->persist($Acces);
            $bdd->flush();
        }

        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/updateAccesAgenda/{uneDate}", name="updateAccesAgenda", defaults={"uneDate"=null})
     */
    public function updateAccesAgenda($uneDate): Response {
        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd 

        $Personne = $bdd->getRepository(Personne::class)->find($_POST['user']);
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['agenda']);

        $User = $this->getUser();
        $myAcces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Personne' => $User, 'Ref_Agendas' => $Agenda));

        if ($myAcces->getIdDroit()->getId() == 1) {
            $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Personne' => $Personne, 'Ref_Agendas' => $Agenda));
            $Droit = $bdd->getRepository(Droit::class)->find($_POST['droit']);

            $Acces->setIdDroit($Droit);
            $bdd->persist($Acces);
            $bdd->flush();
            $jsonData = array('msg' => 'Le droit à été modifier.');
        }
        else {
            $jsonData = array('msg' => 'Vous n\'avez pas les droits.');
        }

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/deleteAccesAgenda/{uneDate}", name="deleteAccesAgenda", defaults={"uneDate"=null})
     */
    public function deleteAccesAgenda($uneDate): Response {
        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd 

        $Personne = $bdd->getRepository(Personne::class)->find($_POST['user']);
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['agenda']);

        $User = $this->getUser();
        $myAcces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Personne' => $User, 'Ref_Agendas' => $Agenda));

        if ($myAcces->getIdDroit()->getId() == 1) {
            $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Personne' => $Personne, 'Ref_Agendas' => $Agenda));

            $bdd->remove($Acces);
            $bdd->flush();
            $jsonData = array('msg' => 'Le droit à été supprimer.');
        }
        else {
            $jsonData = array('msg' => 'Vous n\'avez pas les droits.');
        }

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return new JsonResponse($jsonData);
    }

    /**
     * Route permetant l'insertion d'un ou de plusieurs evenement
     * @Route("/insertEvent/{uneDate}", name="insertEvent", defaults={"uneDate"=null})
     */
    public function insertEvent($uneDate): Response {

        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager();
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['selectAgendaForm']); //Récupération de l'agenda par son id
        $Personne = $this->getUser();
        $Categorie = $bdd->getRepository(Categorie::class)->find($_POST['selectCategorie']); //Récupération de la catégorie par son id
        //Récupération de l'accès par la référence personne et agenda
        $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Agendas' => $Agenda, 'Ref_Personne' => $Personne));

        //Si l'utilisateur peut ecrire alors
        if ($Acces->getIdDroit()->getId() == 1) {
            //Si la récurence, le type recurence et la date de fin n'est pas vide 
            if (!empty($_POST['inputRecurrence']) && !empty($_POST['typeRecurrence']) && !empty($_POST['finRecurrence'])) {
                //Création de l'intervale
                $Interval = new DateInterval('P' . $_POST['inputRecurrence'] . $_POST['typeRecurrence']);
                //Creation de la période
                $DateRange = new DatePeriod(date_create($_POST['dtDebut']), $Interval, date_create($_POST['finRecurrence'] . ' +1 day'));

                //Parcour des dates
                foreach ($DateRange as $uneDateEvent) {
                    //Creation et setting des attributs
                    $Event = new Evenement();
                    $Event->setLibelle($_POST['inputLibelle']);
                    $Event->setNote($_POST['inputNote']);
                    $Event->setLieu($_POST['inputLieu']);
                    $Event->setCouleur($_POST['selectCouleur']);
                    $Event->setDateDebut(date_create($uneDateEvent->format('Y-m-d ') . $_POST['timeDebut']));
                    $Event->setDateFin(date_create($uneDateEvent->format('Y-m-d ') . $_POST['timeFin']));
                    $Event->setAgenda($Agenda);
                    $Event->setCategorie($Categorie);
                    $bdd->persist($Event); //Enregistrement dans le manager
                }
            }
            else { //Si il n'a qu'un evenement a enregistrer
                //Création et setting des attributs
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

        //Met a jour la date si il y'en a une
        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        //Redirection sur la route accueil
        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * @Route("/getEventById", name="getEventById")
     */
    public function getEventById(): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $Event = $bdd->getRepository(Evenement::class)->find($_POST['ref_event']);
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['ref_agenda']);
        $Personne = $bdd->getRepository(Personne::class)->find($_POST['ref_personne']);
        $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Agendas' => $Agenda, 'Ref_Personne' => $Personne));

        $jsonData = array(
            'Ref_Evenements' => $Event->getId(),
            'Libelle'        => $Event->getLibelle(),
            'Note'           => $Event->getNote(),
            'DateDebut'      => $Event->getDateDebut()->format('Y-m-d h:s'),
            'DateFin'        => $Event->getDateFin()->format('Y-m-d h:s'),
            'Lieu'           => $Event->getLieu(),
            'Couleur'        => $Event->getCouleur(),
            'Ref_Agendas'    => $Event->getAgenda()->getId(),
            'Ref_Categorie'  => $Event->getCategorie()->getId(),
            'Id_Droit_D'     => $Acces->getIdDroit()->getId()
        );

        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/updateEvent/{uneDate}", name="updateEvent", defaults={"uneDate"=null})
     */
    public function updateEvent($uneDate): Response {

        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['selectAgendaForm']);
        $Personne = $this->getUser();
        $Categorie = $bdd->getRepository(Categorie::class)->find($_POST['selectCategorie']);
        $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Agendas' => $Agenda, 'Ref_Personne' => $Personne));

        if ($Acces->getIdDroit()->getId() == 1) {
            if (isset($_POST['checkIntervale'])) {
                $Event = $bdd->getRepository(Evenement::class)->find($_POST['updateEvenement']);
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
                $Event = $bdd->getRepository(Evenement::class)->find($_POST['updateEvenement']);

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

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * @Route("/delEvent/{uneDate}", name="delEvent", defaults={"uneDate"=null})
     */
    public function delEvent($uneDate): Response {

        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $Event = $bdd->getRepository(Evenement::class)->find($_POST['supprimerEvenement']);
        $Agenda = $bdd->getRepository(Agenda::class)->find($Event->getAgenda());
        $Personne = $this->getUser();
        $Acces = $bdd->getRepository(Acceder::class)->findOneBy(array('Ref_Agendas' => $Agenda, 'Ref_Personne' => $Personne));

        if ($Acces->getIdDroit()->getId() == 1) {
            if (isset($_POST['checkIntervale'])) {
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

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

    /**
     * @Route("/getNotif", name="getNotif")
     */
    public function getNotif(): Response {
        $jsonData = array();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $User = $this->getUser();
        $query = $bdd->createQuery('SELECT Acc, A, E FROM App\Entity\Acceder Acc JOIN Acc.Ref_Agendas A JOIN A.Evenement E WHERE Acc.Ref_Personne = :Personne ORDER BY E.DateDebut DESC'); //Recherche des droit
        $query->setMaxResults(1);
        $query->setParameter(':Personne', $User);
        $Acces = $query->getResult();

        $Notifs = $Acces[0]->getAgendas()->getEvenement();
        foreach ($Notifs as $uneNotif) {
            $jsonData = array(
                'Libelle'   => $uneNotif->getLibelle(),
                'DateDebut' => $uneNotif->getDateDebut()->format('d-m-Y H:i'),
                'Note'      => $uneNotif->getNote(),
                'Lieu'      => $uneNotif->getLieu()
            );
        }
        return new JsonResponse($jsonData);
    }

}
