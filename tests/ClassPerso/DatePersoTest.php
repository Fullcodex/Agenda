<?php

namespace App\test\ClassPerso;

use App\classPerso\DatePerso;
use PHPUnit\Framework\TestCase;
use DatePeriod;
use DateInterval;

class DatePersoTest extends TestCase {
    //put your code here
    public function testmajDate() {
        $Date = new DatePerso();
        $interval = new DateInterval('P1D');     
        
        $Date->majDate(date('2021-03-06'));      
        
        //Test de la date de debut Renvoie true
        $this->assertEquals(date_create('2021-03-01'), $Date->getdtDebut());
        //Test de la date de fin Renvoie true
        $this->assertEquals(date_create('2021-04-05'), $Date->getdtFin());
        //Test de la date du mois suivant Renvoie true
        $this->assertEquals(date_create('2021-04-01'), $Date->getmoisSui());
        //Test de la date du mois précédent Renvoie true
        $this->assertEquals(date_create('2021-02-01'), $Date->getmoisPre());
        //Test de la periode Renvoie true
        $this->assertEquals(new DatePeriod(date_create('2021-03-01'), $interval, date_create('2021-04-05')), $Date->getDateRage());
        
        $Date->majDate('rerzrzrer');
        //Test de la date de debut Renvoie true
        $this->assertEquals(date_create('2021-03-01'), $Date->getdtDebut());
        //Test de la date de fin Renvoie true
        $this->assertEquals(date_create('2021-04-05'), $Date->getdtFin());
        //Test de la date du mois suivant Renvoie true
        $this->assertEquals(date_create('2021-04-01'), $Date->getmoisSui());
        //Test de la date du mois précédent Renvoie true
        $this->assertEquals(date_create('2021-02-01'), $Date->getmoisPre());
        //Test de la periode Renvoie true
        $this->assertEquals(new DatePeriod(date_create('2021-03-01'), $interval, date_create('2021-04-05')), $Date->getDateRage());
    }
}
