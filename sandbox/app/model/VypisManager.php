<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\model;

use Nette;
use Nette\Utils\DateTime;

/**
 * Objekt obsluhujici Smenu a ridice v praci
 */
class VypisManager {

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function stavyAsArray() {
        $sql = "Select idStav, nazev from Stav";
        $moznosti = array();
        $table = $this->database->table('Stav');
        foreach ($table as $key => $value) {
            $moznosti[$value->nazev] = $value->nazev;
        }
        return $moznosti;
    }

    public function vsichniDispeceriAsArray() {
        $sql = "SELECT  
                    idDispecer,Osoba.prezdivka
                FROM
                    Dispecer,Osoba
                WHERE
                        Osoba.idOsoba = Dispecer.idOsoba";
        $result = $this->database->query($sql);
        $dispceri = array();
        foreach ($result as $key => $value) {
            $dispceri[$value['idDispecer']] = $value['prezdivka'];
        }
        return $dispceri;
    }

    public function vsichniRidiciAsArray() {
        $sql = "SELECT  
                    idRidic,Osoba.prezdivka
                FROM
                    Ridic,Osoba
                WHERE
                        Osoba.idOsoba = Ridic.idOsoba";
        $result = $this->database->query($sql);
        $ridici = array();
        foreach ($result as $key => $value) {
            $ridici[$value['idRidic']] = $value['prezdivka'];
        }
        return $ridici;
    }

    public function vsichniRidici() {
        $sql = "SELECT  
                    idRidic,Osoba.prezdivka
                FROM
                    Ridic,Osoba
                WHERE
                        Osoba.idOsoba = Ridic.idOsoba";
        $result = $this->database->query($sql);
        $ridici = array();
        foreach ($result as $key => $value) {
            $ridici[$value['idRidic']] = $value['prezdivka'];
        }
        return $ridici;
    }

    public function vsechnyAktivniOsoby() {
        $sql = "Select * From Osoba where Osoba.aktivni = 1";
        return $this->database->query($sql);
    }

    public function vsechnyAktivniOsobyAsArray() {
        $result = $this->vsechnyAktivniOsoby(); 
        $osoby = array();
        foreach ($result as $key => $value) {
            $osoby[$value['idOsoba']] = $value['prezdivka'];
        }
        return $osoby;
    }

}
