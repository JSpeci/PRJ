<?php

namespace app\model;

use Nette;
use Nette\Utils\DateTime;

/**
 * Objekt obsluhujici Smenu a ridice v praci
 * 
 * 
 * @author Jan Špecián
 */
class SmenaManager {

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /** Id poslední běžící směny
     * 
     * @return type
     */
    public function getPoslednismenaId() {
        $sql = "SELECT 
                idSmena
            FROM
                Smena
            ORDER BY Smena.do DESC
            LIMIT 1;";
        return $this->database->query($sql)->fetch()['idSmena'];
    }

    /** Zda poslední směna v tabulce je aktuální, nebo už je v minulosti
     * 
     * @return boolean
     */
    public function posledniSmenaTrva() {
        //select posledni smeny
        $sql = "SELECT /* Posledni smena jeste trva = 1, Posledni smena jiz skoncila = 0*/
                    (NOW() - Smena.do) < 0 as 'trva'
                FROM
                    Smena
                ORDER BY Smena.do DESC
                LIMIT 1";
        $result = $this->database->query($sql);

        //nevklada duplicitni smenu
        $table = $result->fetch();

        if ($table['trva'] == 0) {
            return false;
        } else {
            return true;
        }
    }

    /** Poslední směna řádek z tabulky
     * 
     * @return type
     */
    public function posledniSmena() {
        $sql = "SELECT /* Posledni smena */
                        *
                FROM
                        Smena
                ORDER BY Smena.do DESC
                LIMIT 1";
        return $this->database->query($sql)->fetch();
    }

    /** Vytvoří novou směnu, pokud žádná směna neběží.
     * 
     * @param DateTime $_od
     * @param type $_do
     */
    public function createSmena(\Nette\Utils\DateTime $_od, $_do) {

        if (!$this->posledniSmenaTrva()) {

            $hodina = date('H', $_od->getTimestamp());
            $poznamka = "";

            if ($hodina >= 20 || $hodina < 8) { //noční
                $poznamka = "noční";
            }
            if ($hodina >= 8 && $hodina < 20) { //denní
                $poznamka = "denní";
            }
            $sql = "INSERT INTO Smena values(null,?, ?,?);";
            $this->database->query($sql, $_od, $_do, $poznamka);
        }
    }

    /** Propojení směny s osobami
     * 
     * @param type $idOsoba
     */
    public function pridejOsobuDoPosledniSmeny($idOsoba) {
        $idSmena = $this->getPoslednismenaId();
        $sql = "INSERT INTO Osoba_has_Smena(idOsoba, idSmena) VALUES(? ,? );";
        $this->database->query($sql, $idOsoba, $idSmena);
    }

    /** Tabulka osob v poslední směně
     * 
     * @return type
     */
    public function osobyVPosledniSmene() {
        $idSmena = $this->getPoslednismenaId();
        $sql = "SELECT 
                    Osoba.prezdivka, Osoba_has_Smena.idSmena
                FROM
                    Osoba_has_Smena,Osoba
                WHERE
                        Osoba_has_Smena.idOsoba = Osoba.idOsoba
                    AND Osoba_has_Smena.idSmena = ?";
        return $this->database->query($sql, $idSmena);
    }

    /** Osoby, ktere ještě nejsou v poslední směně - pro selectbox pro přidávání lidí do směny
     * 
     * @return type
     */
    public function osobyKtereLzeVlozitDoPosledniSmeny() {
        $idSmena = $this->getPoslednismenaId();
        $sql = "Select Osoba.idOsoba,Osoba.prezdivka From Osoba where Osoba.aktivni = 1 AND idOsoba 
            NOT IN(
                    SELECT 
                        Osoba.idOsoba
                    FROM
                        Osoba_has_Smena,
                        Osoba
                    WHERE
                        Osoba_has_Smena.idOsoba = Osoba.idOsoba
                            AND Osoba_has_Smena.idSmena = ?)";
        return $this->database->query($sql, $idSmena);
    }

    /** TO DO
     * 
     * @param Nette\Utils\DateTime $kdy libovolny cas
     * @return type
     */
    public function smenaIdPodleCasu(Nette\Utils\DateTime $kdy) {
        
    }

}
