<?php
namespace app\model;

use Nette;
use Nette\Utils\DateTime;

/**
 * Objekt pro výpisy z databáze
 * @author Jan Špecián
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

    /** Vypis všech možných stavů osoby
     * 
     * @return type
     */
    public function stavyAsArray() {
        $sql = "Select idStav, nazev from Stav";
        $moznosti = array();
        $table = $this->database->table('Stav');
        foreach ($table as $key => $value) {
            $moznosti[$value->nazev] = $value->nazev;
        }
        return $moznosti;
    }

    /** Výpis dispečerů pro select box
     * 
     * @return type
     */
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

    /** Výpis všech řidičů pro selectbox
     * 
     * @return type
     */
    public function vsichniRidiciAsArray() {
        $result = $this->vsichniRidici();
        $ridici = array();
        foreach ($result as $key => $value) {
            $ridici[$value['idRidic']] = $value['prezdivka'];
        }
        return $ridici;
    }

    /** Všichni řidiči
     * 
     * @return type
     */
    public function vsichniRidici() {
        $sql = "SELECT  
                    idRidic,Osoba.prezdivka
                FROM
                    Ridic,Osoba
                WHERE
                        Osoba.idOsoba = Ridic.idOsoba";
        return $this->database->query($sql);
    }

    /** Aktivní osoby, ty které jsou zaměstnanci
     * 
     * @return type
     */
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
