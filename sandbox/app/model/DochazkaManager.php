<?php

namespace app\model;

use Nette;
use Nette\Utils\DateTime;

/**
 * Objekt obsluhujici Smenu a ridice v praci
 */
class DochazkaManager {

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;
    
    private $logStavu;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->logStavu = new LogStavu($database);
    }

    protected function idOsoba_idRidic($idRidic) {
        $sql = "SELECT 
                    Osoba.idOsoba, Osoba.prezdivka
                FROM
                    Ridic,
                    Osoba
                WHERE
                    Ridic.idOsoba = Osoba.idOsoba
                    AND Ridic.idRidic = '$idRidic'";
        return $this->database->query($sql)->fetch()['idOsoba'];
    }

    protected function idOsoba_idDispecer($idDispecer) {
        $sql = "SELECT 
                    Osoba.idOsoba, Osoba.prezdivka
                FROM
                    Dispecer,
                    Osoba
                WHERE
                    Dispecer.idOsoba = Osoba.idOsoba
                    AND Dispecer.idDispecer = ?";
        return $this->database->query($sql, $idDispecer)->fetch()['idOsoba'];
    }

    public function prichodRidicSluzba($idRidic, \Nette\Utils\DateTime $kdy) {
        $idOsoba = $this->idOsoba_idRidic($idRidic);

        if ($this->idOsobaJeVPraci($idOsoba)) {
            $this->odchodRidice($idRidic, $kdy);
        }

        $sql = "INSERT into RidiciVeSluzbe(RidiciVeSluzbe.idRidic) VALUES(?);";
        $this->database->query($sql, $idRidic);

        $this->prichodOsobaDochazka($idOsoba, $kdy);

        $this->nastavRidiceDoStavu($idRidic, 'volno');
    }

    public function prichodRidicKlouzani($idRidic, \Nette\Utils\DateTime $kdy) {

        $idOsoba = $this->idOsoba_idRidic($idRidic);
        if ($this->idOsobaJeVPraci($idOsoba)) {
            $this->odchodRidice($idRidic, $kdy);
        }

        $sql = "INSERT INTO RidiciOstatniKDispozici(RidiciOstatniKDispozici.idRidic) VALUES(?);";
        $this->database->query($sql, $idRidic);
        
        $this->prichodOsobaDochazka($idOsoba, $kdy);
        $this->nastavRidiceDoStavu($idRidic, 'volno');
    }

    public function prichodDispecer($idDispecer, \Nette\Utils\DateTime $kdy) {
        $idOsoba = $this->idOsoba_idDispecer($idDispecer);
        if ($this->idOsobaJeVPraci($idOsoba)) {
            $this->odchodDispecer($idDispecer, $kdy);
        }
        $this->prichodOsobaDochazka($idOsoba, $kdy);
        $this->nastavDispeceraDoStavu($idDispecer, 'pracuje');
    }

    public function odchodDispecer($idDispecer, \Nette\Utils\DateTime $kdy) {
        $idOsoba = $this->idOsoba_idDispecer($idDispecer);
        $this->odchodOsobaDochazka($idOsoba, $kdy);
        $this->nastavDispeceraDoStavu($idDispecer, 'nepracuje');
    }

    public function nastavDispeceraDoStavu($idDispecer, $stav) {
        $sql = "UPDATE Dispecer 
                SET 
                    Dispecer.idStav = (SELECT 
                            idStav
                        FROM
                            Stav
                        WHERE
                            Stav.nazev = ?)
                WHERE Dispecer.idDispecer = ?";
        $this->database->query($sql, $stav, $idDispecer);
        // do LoguStavu
        $idOsoba = $this->idOsoba_idDispecer($idDispecer);
        $kdy = DateTime::from(time());
        $this->logStavu->zmenaStavu($kdy, $idOsoba, $stav);
    }

    public function nastavRidiceDoStavu($idRidic, $stav) {
        $sql = "UPDATE Ridic 
                SET 
                    Ridic.idStav = (SELECT 
                            idStav
                        FROM
                            Stav
                        WHERE
                            Stav.nazev = ?)
                WHERE Ridic.idRidic = ?";
        $this->database->query($sql, $stav, $idRidic);
        // do LoguStavu
        $idOsoba = $this->idOsoba_idRidic($idRidic);
        $kdy = DateTime::from(time());
        $this->logStavu->zmenaStavu($kdy, $idOsoba, $stav);
    }

    protected function prichodOsobaDochazka($idOsoba, \Nette\Utils\DateTime $kdy) {
        $sql = "INSERT INTO Dochazka VALUES(NULL,?,NOW(),NULL);";
        $this->database->query($sql, $idOsoba);
    }

    protected function odchodOsobaDochazka($idOsoba, \Nette\Utils\DateTime $kdy) {
        $sql = "Update Dochazka SET Dochazka.odchod = NOW() WHERE odchod is null AND idOsoba = ?";
        $this->database->query($sql, $idOsoba);
    }

    public function odchodRidice($idRidic, \Nette\Utils\DateTime $kdy) {

        $sql = "DELETE FROM RidiciOstatniKDispozici WHERE idRidic = ?";
        $this->database->query($sql, $idRidic);

        $sql = "DELETE FROM RidiciVeSluzbe WHERE idRidic = ?";
        $this->database->query($sql, $idRidic);

        $idOsoba = $this->idOsoba_idRidic($idRidic);
        $this->odchodOsobaDochazka($idOsoba, $kdy);

        $this->nastavRidiceDoStavu($idRidic, 'nepracuje');
    }

    public function idOsobaJeVPraci($idOsoba) {
        $sql = "SELECT COUNT(idOsoba) as 'jeVPraci' from Dochazka WHERE odchod is null AND idOsoba = ? ";
        $jeVPraci = $this->database->query($sql, $idOsoba)->fetch()['jeVPraci'];
        if ($jeVPraci == 1) {
            return true;
        } elseif ($jeVPraci == 0) {
            return false;
        } else {
            throw new Exception("Osoba idOsoba:" . $idOsoba . "ma vice prichodu, nez odchodu - nekonzistence v tabulce!");
        }
    }
    
    public function ridiciVPraci() {

        $sql = "SELECT /* Z lidi, kteri jsou pritomni v praci vytahnu ty, kteri jsou ridici*/ 
                    Ridic.idRidic, Osoba.prezdivka
                FROM
                    Dochazka,
                    Ridic,
                    Osoba
                WHERE
                    Dochazka.odchod IS NULL
                        AND Dochazka.idOsoba IN (SELECT 
                            Ridic.idOsoba
                        FROM
                            Ridic)
                        AND Ridic.idOsoba = Osoba.idOsoba
                        AND Dochazka.idOsoba = Ridic.idOsoba;";
        return $this->database->query($sql);
    }

    /**
     * metoda AsArray pro pouziti v SelectBoxu
     * @return type
     */
    public function ridiciVPraciAsArray() {
        $result = $this->ridiciVPraci();
        $ridici = array();
        foreach ($result as $key => $value) {
            $ridici[$value['idRidic']] = $value['prezdivka'];
        }
        return $ridici;
    }

    public function ridiciVeSluzbe() {
        $sql = "SELECT 
                    Ridic.idRidic, Osoba.prezdivka, Stav.nazev as 'stav', Osoba.idOsoba as 'idOsoba'
                FROM
                    RidiciVeSluzbe,
                    Ridic,
                    Osoba,
                    Stav
                WHERE
                    RidiciVeSluzbe.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Stav.idStav = Ridic.idStav";
        return $this->database->query($sql);
    }
    
    public function ridiciKlouzani() {
        $sql = "SELECT 
                   Ridic.idRidic, Osoba.prezdivka, Stav.nazev as 'stav', Osoba.idOsoba as 'idOsoba'
                FROM
                    RidiciOstatniKDispozici,
                    Ridic,
                    Osoba,
                    Stav
                WHERE
                    RidiciOstatniKDispozici.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Stav.idStav = Ridic.idStav";
        return $this->database->query($sql);
    }


    public function dispeceriVPraciAsArray() {
        $result = $this->dispeceriVPraci();

        $dispceri = array();
        foreach ($result as $key => $value) {
            $dispceri[$value['idDispecer']] = $value['prezdivka'];
        }
        return $dispceri;
    }

    public function dispeceriVPraci() {

        $sql = "SELECT /* Z lidi, kteri jsou pritomni v praci vytahnu ty, kteri jsou dispeceri*/
                    Dispecer.idDispecer, Osoba.prezdivka, Stav.nazev as 'stav'
                FROM
                    Dochazka,
                    Dispecer,
                    Osoba,
                    Stav
                WHERE
                    Dochazka.odchod IS NULL
                        AND Dochazka.idOsoba IN (SELECT 
                            Dispecer.idOsoba
                        FROM
                            Dispecer)
                        AND Dispecer.idOsoba = Osoba.idOsoba
                        AND Dochazka.idOsoba = Dispecer.idOsoba
                        AND Stav.idStav = Dispecer.idStav;";
        return $this->database->query($sql);
    }

    public function pocetVeSluzbe() {
        $sql = "Select COUNT(idRidic) as 'pocetVeSluzbe' from RidiciVeSluzbe";
        $pocet = $this->database->query($sql)->fetch()['pocetVeSluzbe'];
        return $pocet;
    }

    public function pocetNaKlouzani() {
        $sql = "Select COUNT(idRidic) as 'pocetKlouzaku' from RidiciOstatniKDispozici";
        $pocet = $this->database->query($sql)->fetch()['pocetKlouzaku'];
        return $pocet;
    }

    public function pocetDispeceru() {
        $sql = "SELECT 
                    COUNT(Dispecer.idDispecer) AS 'pocetDispeceruVPraci'
                FROM
                    Dispecer
                WHERE
                    Dispecer.idOsoba IN (
                                SELECT 
                            Osoba.idOsoba
                        FROM
                            Osoba
                        WHERE
                            Osoba.idOsoba IN (
                                                SELECT 
                                    Dochazka.idOsoba
                                FROM
                                    Dochazka
                                WHERE
                                    Dochazka.odchod IS NULL))";
        $pocet = $this->database->query($sql)->fetch()['pocetDispeceruVPraci'];
        return $pocet;
    }

}
