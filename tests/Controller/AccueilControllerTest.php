<?php

namespace App\Tests\Controller;

use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AccueilControllerTest
 *
 * @author FullCodex
 */
class AccueilControllerTest extends WebTestCase {
      
    public function testsharedAgendaPost()
    {
        //Simulation de navigateur
        $client = static::createClient();
        $userRepository = static::$container->get(PersonneRepository::class);
        
        // Recupère un utilisateur test
        $testUser = $userRepository->findOneByEmail('fullcodex974@gmail.com');

        // Simule la connexion de l'utilisateur
        $client->loginUser($testUser);

        //Envoie de la requete POST
        $client->request('POST', '/shareAgenda', ['mail'=>'happynutty@gmail.com', 'expediteur' => 4, 'agenda'=> 5]);  

        //Verification de la reponse
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //Verification du type de page renvoyer
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
