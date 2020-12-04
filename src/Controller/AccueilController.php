<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\LoginFormAuthenticator;
use App\Entity\Personne;
use App\Entity\Acceder;
use App\Entity\Agenda;
use App\Entity\Evenement;
use App\Entity\Pj;
use App\Entity\Categorie;
use App\Entity\Droit;
use App\classPerso\DatePerso;
use function dump;

class AccueilController extends AbstractController {

    /**
     * @Route("/accueil/{uneDate}/{idAgendaSelect}", name="accueil", defaults={"uneDate"=null, "idAgendaSelect"=null})
     */
    public function index($uneDate, $idAgendaSelect): Response {
        $Date = new DatePerso();

        $selectedAgenda = null;
        $allAcces = array();
        $myAcces = false;

        //Connexion base de donnée
        $bdd = $this->getDoctrine()->getManager();

        //Recupération de l'utilisateur connecté
        $User = $this->getUser();

        $query1 = $bdd->createQuery('SELECT Acc, A FROM App\Entity\Acceder Acc JOIN Acc.Ref_Agendas A WHERE Acc.Ref_Personne = :Personne'); //Recherche des agendas
        $query1->setParameter(':Personne', $User);
        $allAgendas = $query1->getResult();

        $query2 = $bdd->createQuery('SELECT D FROM App\Entity\Droit D'); //Recherche des droits
        $allDroit = $query2->getResult();

        $query3 = $bdd->createQuery('SELECT C FROM App\Entity\Categorie C'); //Recherche des categories
        $allCategorie = $query3->getResult();

        if (isset($_POST['selectAgenda']) || $idAgendaSelect !== null) {
            $idAgendaSelect = $_POST['selectAgenda'] ?? $idAgendaSelect;
            if ($idAgendaSelect !== 'all') {
                foreach ($allAgendas as $unAgenda) {
                    if ($unAgenda->getAgendas()->getId() == $idAgendaSelect) {
                        $selectedAgenda = $unAgenda->getAgendas();

                        $query3 = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE A.id = :Agenda '); //Recherche des droit
                        $query3->setParameter(':Agenda', $selectedAgenda);
                        $allEvents = $query3->getResult();

                        $query4 = $bdd->createQuery('SELECT Acc, P, D FROM App\Entity\Acceder Acc JOIN Acc.Ref_Agendas A JOIN Acc.Ref_Personne P JOIN Acc.Id_Droit D WHERE Acc.Ref_Agendas = :Agenda'); //Recherche des droit
                        $query4->setParameter(':Agenda', $selectedAgenda);
                        $allAcces = $query4->getResult();

                        dump($allAcces);

                        break;
                    }
                }

                $myAcces = false;
                foreach ($allAcces as $unAcces) {
                    if ($unAcces->getRefPersonne()->getId() == $User->getId()) {
                        if ($unAcces->getIdDroit()->getId() == 1) {
                            $myAcces = true;
                        }
                    }
                }
            } else {

                foreach ($allAgendas as $unAgenda) {
                    $query4 = $bdd->createQuery('SELECT E FROM App\Entity\Evenement E JOIN E.Agenda A WHERE A.id = :Agenda'); //Recherche des droit
                    $query4->setParameter(':Agenda', $unAgenda->getAgendas());
                    $allEvents = $query4->getResult();
                }

//                dump($allEvents);
            }
        }

//        dump($User);
        dump($allAgendas);
//        dump($unAgenda);


        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return $this->render('accueil/index.html.twig', [
                    'User' => $User,
                    'Date' => $Date,
                    'allAgendas' => $allAgendas,
                    'allDroits' => $allDroit,
                    'allCategorie' => $allCategorie,
                    'selectedAgenda' => $selectedAgenda,
                    'allAcces' => $allAcces,
                    'myAcces' => $myAcces
        ]);
    }

    /**
     * @Route("/updateUser/{uneDate}/{id}", name="updateUser", defaults={"uneDate"=null})
     */
    public function updateUser($uneDate, Personne $User, LoginFormAuthenticator $authenticator): Response {

        $Date = new DatePerso();

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        $User->setNomPrenom($_POST['inputNomUser']);
        $User->setEmail($_POST['inputEmailUser']);
        $User->setDateNaissance($_POST['inputDtNaissUser']);

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
     * @Route("/insertAgenda/{uneDate}/{id}", name="insertAgenda", defaults={"uneDate"=null})
     */
    public function insertAgenda($uneDate, Personne $User): Response {

        $Date = new DatePerso();
        $Agenda = new Agenda(); //Instanciation d'un nouvelle agenda
        $Acces = new Acceder(); //Instanciation d'un nouvelle acces
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

        dump($allAcces);
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
     * @Route("/insertEvent/{uneDate}", name="insertEvent", defaults={"uneDate"=null})
     */
    public function insertEvent($uneDate): Response {

        $Date = new DatePerso();
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd
        $Agenda = $bdd->getRepository(Agenda::class)->find($_POST['selectAgenda']);
        $Categorie = $bdd->getRepository(Categorie::class)->find($_POST['selectCategorie']);

        if (!empty($_POST['inputRecurrence']) && !empty($_POST['typeRecurrence']) && !empty($_POST['finRecurrence'])) {
            $Interval = new DateInterval('P' . $_POST['inputRecurrence'] . $_POST['typeRecurrence']);
            $DateRange = new DatePeriod(date_create($_POST['dtDebut']), $Interval, date_create($_POST['finRecurrence']));

            foreach ($DateRange as $uneDateEvent) {
                $Event = new Evenement();
                $Event->setLibelle($_POST['inputLibelle']);
                $Event->setNote($_POST['inputNote']);
                $Event->setLieu($_POST['inputLieu']);
                $Event->setCouleur($_POST['selectCouleur']);
                $Event->setDateDebut($uneDateEvent->format('Y-m-d ') . $_POST['timeDebut']);
                $Event->setDateFin($uneDateEvent->format('Y-m-d ') . $_POST['timeFin']);
                $Event->setAgenda($Agenda);
                $Event->setCategorie($Categorie);
                $bdd->persist($Event);
            }
        } else {
            $Event = new Evenement();
                $Event->setLibelle($_POST['inputLibelle']);
                $Event->setNote($_POST['inputNote']);
                $Event->setLieu($_POST['inputLieu']);
                $Event->setCouleur($_POST['selectCouleur']);
                $Event->setDateDebut($_POST['dtDebut'] . $_POST['timeDebut']);
                $Event->setDateFin($_POST['dtDebut'] . $_POST['timeFin']);
                $Event->setAgenda($Agenda);
                $Event->setCategorie($Categorie);
                $bdd->persist($Event);
        }

        $bdd->flush(); //Enregistre les changements dans la bdd

        if (!empty($uneDate)) {
            $Date->majDate($uneDate);
        }

        return $this->redirectToRoute('accueil', ['Date' => $Date]);
    }

}
