<?php

namespace App\classPerso;

use DateInterval;
use DatePeriod;
use DateTime;

class DatePerso {

    private $dtDebut; //Date de debut de l'agenda
    private $dtFin; //Date de fin de l'agenda
    private $dateRage; //Range de parcour
    private $moisPre;   //Mois précédent
    private $moisSui;   //Mois suivant
    private $moisCourant;   //Mois courant pour afficher
    private $interval;

    public function __construct() {
        $this->interval = new DateInterval('P1D'); //Creation de l'intervale d'un jour
        $this->dtDebut = date_create('first day of')->modify('monday this week');
        $this->dtFin = date_create('last day of')->modify('sunday this week')->add($this->interval);
        $this->dateRage = new DatePeriod($this->dtDebut, $this->interval, $this->dtFin);
        $this->moisPre = date_create('first day of ' . date('Y-m-d'))->modify('last month');
        $this->moisSui = date_create('first day of ' . date('Y-m-d'))->modify('next month');
        $this->moisCourant = date_create();
    }

    public function majDate($date) {
        $this->dtDebut = date_create('first day of ' . $date)->modify('monday this week');
        $this->dtFin = date_create('last day of ' . $date)->modify('sunday this week')->add($this->interval);
        $this->dateRage = new DatePeriod($this->dtDebut, $this->interval, $this->dtFin);
        $this->moisPre = date_create('first day of ' . $date)->modify('last month');
        $this->moisSui = date_create('first day of ' . $date)->modify('next month');
        $this->moisCourant = date_create('first day of ' . $date);
    }

    public function tradFr() {
        //Mois francais
        $monthFr = array(
            'January'   => 'Janvier',
            'February'  => 'Février',
            'March'     => 'Mars',
            'April'     => 'Avril',
            'May'       => 'Mai',
            'June'      => 'Juin',
            'July'      => 'Juillet',
            'August'    => 'Août',
            'September' => 'Semptembre',
            'October'   => 'Octobre',
            'November'  => 'Novembre',
            'December'  => 'Décembre'
        );
        
        return $monthFr[$this->moisCourant->format('F')];
    }
    
    public function getdtDebut(){
        return $this->dtDebut;
    }
    
    public function getdtFin(){
        return $this->dtFin;
    }
    
    public function getDateRage(){
        return $this->dateRage;
    }
    public function getmoisPre(){
        return $this->moisPre;
    }
    public function getmoisSui(){
        return $this->moisSui;
    }
    public function getmoisCourant(){
        return $this->moisCourant;
    }
}
