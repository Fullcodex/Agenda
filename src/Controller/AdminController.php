<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
    /**
     * @Route("/admin/user", name="user")
     */
    public function gesionUser(): Response
    {
        return $this->render('admin/gestionUser.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
