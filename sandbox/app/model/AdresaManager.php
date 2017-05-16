<?php

namespace app\model;

use Nette;
use Nette\Utils\DateTime;

/**
 * Třída pro práci s Adresami v databázi
 *
 * @author Jan Špecián
 */
class AdresaManager {

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /** Vrátí řádek podle idAdresa
     * 
     * @param type $idAdresa
     * @return type
     */
    public function getAdresaById($idAdresa) {
        $sql = "SELECT 
                    *
                FROM
                    Adresa
                WHERE
                    Adresa.idAdresa = ? ";
        return $this->database->query($sql, $idAdresa);
    }

    /** Vrátí řádek podle ulice, cislo, mesto
     * 
     * @param type $ulice
     * @param type $cislo
     * @param type $mesto
     * @return type
     */
    public function getAdresaByUliceCisloMesto($ulice, $cislo, $mesto) {
        $sql = "SELECT 
                    *
                FROM
                    Adresa
                WHERE
                    Adresa.ulice = ?
                        AND Adresa.cislo = ?
                        AND Adresa.mesto = ?";
        return $this->database->query($sql, $ulice, $cislo, $mesto);
    }

    /** Existuje tato adresa v tabulce?
     * 
     * @param type $ulice
     * @param type $cislo
     * @param type $mesto
     * @return boolean
     */
    public function znamTutoAdresu($ulice, $cislo, $mesto) {
        $sql = "SELECT 
                    COUNT(idAdresa) >= 1 as 'znam'
                FROM
                    Adresa
                WHERE
                    Adresa.ulice = ?
                        AND Adresa.cislo = ?
                        AND Adresa.mesto = ?";
        $res = $this->database->query($sql, $ulice, $cislo, $mesto)->fetch()['znam'];
        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }

    /** Vlozi novou adresu do tabulky Adresa
     * 
     * @param type $ulice
     * @param type $cislo
     * @param type $mesto
     */
    public function novaAdresa($ulice, $cislo, $mesto) {
        if (!$this->znamTutoAdresu($ulice, $cislo, $mesto)) {
            $sql = "INSERT INTO Adresa 
                (idAdresa ,ulice, cislo, mesto, pocetOdjezdu, pocetPrijezdu, gps_lat, gps_lgn, poznamka) 
                VALUES 
                (NULL, ?, ?, ?, 1, 1, NULL, NULL, NULL);";
            $this->database->query($sql, $ulice, $cislo, $mesto);
        }
    }

    /** Vlozi nove misto - alias Adresy - do tabulky Misto
     * 
     * @param type $ulice
     * @param type $cislo
     * @param type $mesto
     * @param type $nazev
     */
    public function pridejMisto($ulice, $cislo, $mesto, $nazev) {
        //pokud ji neznam, tak ji vytvorim
        if (!$this->znamTutoAdresu($ulice, $cislo, $mesto)) {
            $sql = "INSERT INTO Adresa 
                (idAdresa ,ulice, cislo, mesto, pocetOdjezdu, pocetPrijezdu, gps_lat, gps_lgn, poznamka) 
                VALUES 
                (NULL, ?, ?, ?, 1, 1, NULL, NULL, NULL);";
            $this->database->query($sql, $ulice, $cislo, $mesto);
        }
        $idAdresa = $this->getAdresaByUliceCisloMesto($ulice, $cislo, $mesto)->fetch()['idAdresa'];
        $sql = "INSERT INTO Misto VALUES(NULL,?,?)";
        $this->database->query($sql, $idAdresa, $nazev);
    }

    /** Vypíše všechna místa-aliasy Adres
     * 
     * @return type
     */
    public function vsechnaMista() {
        $sql = "SELECT 
                    idAdresa, nazev
                FROM
                    Misto";
        return $this->database->query($sql);
    }

    /** Všechna místa-aliasy Adres jako pole [idAdresa] = nazevMista pro použití v SelectBoxu
     * 
     * @return type
     */
    public function vsechnaMistaAsArray() {
        $res = $this->vsechnaMista();
        $mista = array();
        foreach ($res as $key => $val) {
            $mista[$val->idAdresa] = $val->nazev;
        }
        return $mista;
    }

    /** Vrátí idAdresa podle místa-aliasu
     * 
     * @param type $nazev
     * @return type
     */
    public function getIdAdresaMisto($nazev) {
        $sql = "select idAdresa FROM Misto WHERE nazev = ?";
        return $this->database->query($sql, $nazev)->fetch()['idAdresa'];
    }

}
