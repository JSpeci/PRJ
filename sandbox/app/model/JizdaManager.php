<?php
namespace app\model;


use Nette;
use Nette\Utils\DateTime;
use app\model\AdresaManager;;
use app\model\SmenaManager;
/**
 * Description of Jizda
 *
 * @author King
 */
class JizdaManager {

    //put your code here

    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;
    private $sm;
    private $am;
    private $dm;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->sm = new SmenaManager($database);
        $this->am = new AdresaManager($database);
       $this->dm = new DochazkaManager($database);
    }
    
    public function getJizdaById($idJizda)
    {
        
    }
    
    public function probihajiciJizdy()
    {
        $sql = "SELECT 
                    Ridic.idRidic as 'idRidic',
                    Jizda.idJizda as 'idJizda',
                    prezdivka,
                    casStart,
                    A1.ulice AS 'uliceOd',
                    A1.cislo AS 'cisloOd',
                    A1.mesto AS 'mestoOd',
                    A2.ulice AS 'uliceDo',
                    A2.cislo AS 'cisloDo',
                    A2.mesto AS 'mestoDo'
                FROM
                    Jizda,
                    Ridic,
                    Osoba,
                    Adresa AS A1,
                    Adresa AS A2
                WHERE
                    casKonec IS NULL
                        AND Ridic.idRidic = Jizda.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND A1.idAdresa = Jizda.idAdresaOdkud
                        AND A2.idAdresa = Jizda.idAdresaKam
                ORDER BY Jizda.casStart ASC";
        return $this->database->query($sql);
    }
    
    public function novaJizda($idRidic,$idAdresa_od,$idAdresa_kam, \Nette\Utils\DateTime $cas_start, $idObjednavka = NULL){
        
        $aktualniSmenaId = $this->sm->getPoslednismenaId();
        $sql = "INSERT INTO Jizda (idJizda, idRidic, idAdresaOdkud, idAdresaKam, casStart, casKonec, idSmena, idObjednavka, pribliznaCena, pocetOsob) 
                VALUES 
                (NULL, ?, ?, ?, ?, NULL, ?, ?, '100', '1');";
        $this->database->query($sql,$idRidic,$idAdresa_od,$idAdresa_kam,$cas_start,$aktualniSmenaId,$idObjednavka);
        $this->dm->nastavRidiceDoStavu($idRidic, 'obsazeno');
    }
    
    
    public function konecProbihajiciJizdy($idJizda,\Nette\Utils\DateTime $cas_konec)
    {
        
        $sql = "SELECT idRidic FROM Jizda WHERE Jizda.idJizda = ?";
        $idRidic = $this->database->query($sql,$idJizda)->fetch()['idRidic'];
        $sql="UPDATE Jizda SET casKonec = ? WHERE idJizda = ?";
        $this->database->query($sql,$cas_konec,$idJizda);
        $this->dm->nastavRidiceDoStavu($idRidic, 'volno');
    }

    public function vsechnyJizdyRidice($idRidic,$limit)
    {
        $sql = "SELECT 
                    Ridic.idRidic AS 'idRidic',
                    Jizda.idJizda AS 'idJizda',
                    prezdivka,
                    casStart,
                    A1.ulice AS 'uliceOd',
                    A1.cislo AS 'cisloOd',
                    A1.mesto AS 'mestoOd',
                    A2.ulice AS 'uliceDo',
                    A2.cislo AS 'cisloDo',
                    A2.mesto AS 'mestoDo'
                FROM
                    Jizda,
                    Ridic,
                    Osoba,
                    Adresa AS A1,
                    Adresa AS A2
                WHERE
                        Ridic.idRidic = Jizda.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND A1.idAdresa = Jizda.idAdresaOdkud
                        AND A2.idAdresa = Jizda.idAdresaKam
                        AND Ridic.idRidic = ?
                ORDER BY Jizda.casStart ASC
                LIMIT ?";
        return $this->database->query($sql,$idRidic,$limit);
    }
}
