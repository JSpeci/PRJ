<?php
namespace app\model;

use Nette;
use app\model\Exception;

/** Objekt pro zalogování změny stavu libovolné osoby v práci
 * 
 */
class LogStavu {

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }
    
    /** zaloguje změnu stavu dané osoby
     * 
     * @param \Nette\Utils\DateTime $kdy
     * @param type $idOsoba
     * @param type $nazevStavu
     * @throws Exception
     */
    public function zmenaStavu(\Nette\Utils\DateTime $kdy, $idOsoba, $nazevStavu)
    {
        //kontrola zda daný stav existuje
       $sql="Select COUNT(idStav) as 'pocetStavu' FROM Stav WHERE Stav.nazev = ?";
       $pocetStavu = $this->database->query($sql,$nazevStavu)->fetch()['pocetStavu'];
       if($pocetStavu != 1)
       {
           throw new Exception("LogStavu nalezl více stavů pro vstupní nazev stavu");
       }
       //kontrola, zda daná osoba existuje
       $sql="Select COUNT(idOsoba) as 'pocetOsob' FROM Osoba WHERE Osoba.idOsoba = ?";
       $pocetOsob = $this->database->query($sql,$idOsoba)->fetch()['pocetOsob'];
       if($pocetOsob != 1)
       {
           throw new Exception("LogStavu nalezl více osob pro vstupní idOsoba");
       }
       if($pocetStavu == 1 && $pocetOsob == 1)
       {
           $sql="Select idStav FROM Stav WHERE Stav.nazev = ?";
           $idStav = $this->database->query($sql,$nazevStavu)->fetch()['idStav'];
           
           $sql = "INSERT INTO LogStavu (idLog, idOsoba, kdy, poznamka, idStav) VALUES (NULL, ?, NOW(), NULL, ?)";
           $this->database->query($sql,$idOsoba,$idStav);
       }
    }
    
    /** Výpis logu
     * 
     * @param \Nette\Utils\DateTime $od
     * @param type $do
     * @return type
     */
    public function logOdDo(\Nette\Utils\DateTime $od,$do)
    {
        $sql = "SELECT 
                    Stav.nazev, Osoba.prezdivka, LogStavu.kdy
                FROM
                    LogStavu,
                    Osoba,
                    Stav
                WHERE
                        LogStavu.kdy >= ? AND
                    LogStavu.kdy <= ? AND
                    Osoba.idOsoba = LogStavu.idOsoba
                        AND Stav.idStav = LogStavu.idStav";
        return $this->database->query($sql,$od,$do);
    }
}
