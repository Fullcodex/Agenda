<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Personne;
use App\Form\AdminUserFormType;

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
    public function gestionUser(Request $request): Response {
        $bdd = $this->getDoctrine()->getManager(); //Recupération de la connexion a la bdd

        if (!empty($_POST)) {
            $User = new Personne();
            
            $formRequest = $this->createForm(AdminUserFormType::class, $User);
            $formRequest->handleRequest($request);
            
            dump($formRequest);

            if ($formRequest->isSubmitted() && $formRequest->isValid()) {

                $bdd->persist($User);
                $bdd->flush();
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
        
        dump($AllUser);

        $formInsert = $this->createForm(AdminUserFormType::class);

        return $this->render('admin/gestionUser.html.twig', [
                    'AllUser'        => $AllUser,
                    'formUserInsert' => $formInsert->createView(),
                    'formUserMaj'    => $formMaj
        ]);
    }

}
