<?php
namespace app\model;
use Nette;
use Nette\Utils\DateTime;
/**
 * Description of AdresaManager
 *
 * @author King
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
    
    public function getAdresaById($idAdresa)
    {
        $sql = "SELECT 
                    *
                FROM
                    Adresa
                WHERE
                    Adresa.idAdresa = ? ";
        return $this->database->query($sql,$idAdresa);
    }
    public function getAdresaByUliceCisloMesto($ulice,$cislo,$mesto)
    {
        $sql = "SELECT 
                    *
                FROM
                    Adresa
                WHERE
                    Adresa.ulice = ?
                        AND Adresa.cislo = ?
                        AND Adresa.mesto = ?";
        return $this->database->query($sql,$ulice,$cislo,$mesto);
    }
    public function znamTutoAdresu($ulice,$cislo,$mesto)
    {
        $sql = "SELECT 
                    COUNT(idAdresa) >= 1 as 'znam'
                FROM
                    Adresa
                WHERE
                    Adresa.ulice = ?
                        AND Adresa.cislo = ?
                        AND Adresa.mesto = ?";
        $res = $this->database->query($sql,$ulice,$cislo,$mesto)->fetch()['znam'];
        if($res == 1){
            return true;
        }
        else{
            return false;
        }
    }
    public function novaAdresa($ulice,$cislo,$mesto)
    {
        if(!$this->znamTutoAdresu($ulice, $cislo, $mesto))
        {
            $sql="INSERT INTO Adresa 
                (idAdresa ,ulice, cislo, mesto, pocetOdjezdu, pocetPrijezdu, gps_lat, gps_lgn, poznamka) 
                VALUES 
                (NULL, ?, ?, ?, 1, 1, NULL, NULL, NULL);";
            $this->database->query($sql,$ulice,$cislo,$mesto);
        }
    }
    public function pridejMisto($ulice,$cislo,$mesto,$nazev)
    {
        //pokud ji neznam, tak ji vytvorim
        if(!$this->znamTutoAdresu($ulice, $cislo, $mesto))
        {
            $sql="INSERT INTO Adresa 
                (idAdresa ,ulice, cislo, mesto, pocetOdjezdu, pocetPrijezdu, gps_lat, gps_lgn, poznamka) 
                VALUES 
                (NULL, ?, ?, ?, 1, 1, NULL, NULL, NULL);";
            $this->database->query($sql,$ulice,$cislo,$mesto);
        }
        $idAdresa = $this->getAdresaByUliceCisloMesto($ulice, $cislo, $mesto)->fetch()['idAdresa'];
        $sql = "INSERT INTO Misto VALUES(NULL,?,?)";
        $this->database->query($sql,$idAdresa,$nazev);
    }
    
    public function vsechnaMista()
    {
        $sql = "SELECT 
                    idAdresa, nazev
                FROM
                    Misto"; 
        return $this->database->query($sql);
    }
    
    public function vsechnaMistaAsArray()
    {
        $res = $this->vsechnaMista();
        $mista = array();
        foreach($res as $key => $val)
        {
            $mista[$val->idAdresa] = $val->nazev;
        }
        return $mista;
    }
    
    public function getIdAdresaMisto($nazev)
    {
        $sql="select idAdresa FROM Misto WHERE nazev = ?";
        return $this->database->query($sql,$nazev)->fetch()['idAdresa'];
    }
}
