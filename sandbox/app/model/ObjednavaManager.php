<?php

namespace app\model;
use Nette;
use \Nette\Utils\DateTime;
/**
 * Objekt řešící objednávky a jejich propojení s jízdami
 *
 * @author Jan Špecián
 */
class ObjednavaManager {

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }
    
    /** Nová objednávka
     * 
     * @param DateTime $casPristaveniVozu
     * @param type $idAdresa
     */
    public function vytvoritObjednavku(\Nette\Utils\DateTime $casPristaveniVozu, $idAdresa)
    {
         $casTed = DateTime::from(time()); //nyni
         $sql = "INSERT INTO Objednavka (idObjednavka, casVytvoreni, casPristaveniTaxi, idAdresa, pocetVozu, poznamka, vyrizena) 
                    VALUES 
                 (NULL, ?, ?, ?, '1', NULL, '0')";
         $this->database->query($sql,$casTed,$casPristaveniVozu,$idAdresa);
    }
    
    /** Vypis nevyřízených objednávek
     * 
     * @return type
     */
    public function nevyrizeneObjednavky()
    {
        $sql = "SELECT
                    Objednavka.idObjednavka as 'idObjednavka',
                    Objednavka.casPristaveniTaxi as 'cas',
                    A2.ulice as 'ulice',
                    A2.cislo as 'cislo',
                    A2.mesto as 'mesto'
                FROM
                    Objednavka,
                    Adresa AS A2
                WHERE
                    Objednavka.vyrizena = 0
                        AND Objednavka.idAdresa = A2.idAdresa
                ORDER BY casPristaveniTaxi ASC";
        return $this->database->query($sql);
    }
    
    /** Vytáhne idAresa  objednávky
     * 
     * @param type $idObjednavka
     * @return type
     */
    public function getIdAdresa($idObjednavka)
    {
        $sql = "SELECT 
                    idAdresa
                FROM
                    Objednavka
                WHERE
                    Objednavka.idObjednavka = ?";
        return $this->database->query($sql,$idObjednavka)->fetch()['idAdresa'];
    }
    
    /** Vyřídí objednávku - nastaví jí sloupec vyrizena na 1
     * 
     * @param type $idObjednavka
     * @return type
     */
    public function vyriditObjednavku($idObjednavka)
    {
        $sql = "UPDATE
                    Objednavka
                SET
                    Objednavka.vyrizena = 1
                WHERE
                    Objednavka.idObjednavka = ?";
        return $this->database->query($sql,$idObjednavka);
    }
}
