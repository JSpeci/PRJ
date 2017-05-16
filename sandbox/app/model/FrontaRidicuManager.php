<?php

namespace app\model;

/**
 * Description of FrontaRidicuNaObjednavku
 * třída reprezentující pořadí, v jakém jsou na řadě řidiči na další příchozí objednávku
 * Pokud je nějaký řidič ve stavu volný, nebo vedle, je ve frontě
 * 
 * Fronta se skládá ze dvou front za sebou
 * Nejprve řidiči ve službe, po nich řidiči mimo službu.
 * Na prvním místě je řidič ve službě, který ze všech řidičů ve službě nedostal obědnávku nejdéle
 * až po řidiče, který objednávku dostal naposledy
 * Stejně s řidiči mimo službu
 *
 * @author Jan Špecián
 */
use Nette;
use Nette\Application\UI\Form;
use app\model\SmenaManager;
use app\model\DochazkaManager;
use app\model\VypisManager;
use \app\model\JizdaManager;
use Nette\Utils\DateTime;

class FrontaRidicuManager {

    private $database;
    private $sm;
    private $dm;
    private $vm;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->sm = new SmenaManager($database);
        $this->dm = new DochazkaManager($database);
        $this->vm = new VypisManager($database);
    }

    public function getAktualniFrontaRidicu() {

        //vytahnu ridice ve sluzbe a setridim je podle casu posledni objednavky, kterou dostali
        //select pro ridice, kteri jeste nemaji zadnou objednavku - nováček, nebo po založení databáze
        //se přidají na začátek fronty
        $sql = "SELECT DISTINCT
                    Ridic.idRidic, prezdivka
                FROM
                    RidiciVeSluzbe,
                    Ridic,
                    Jizda,
                    Stav,
                    Osoba
                WHERE
                                RidiciVeSluzbe.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Ridic.idStav = Stav.idStav
                        AND (Stav.nazev = 'volno'
                        OR Stav.nazev = 'vedle')
                        AND Ridic.idRidic NOT IN(SELECT DISTINCT Ridic.idRidic
                                FROM
                                    RidiciVeSluzbe,
                                    Ridic,
                                    Jizda,
                                    Stav,
                                    Osoba
                                WHERE
                                    Jizda.idRidic = RidiciVeSluzbe.idRidic
                                        AND RidiciVeSluzbe.idRidic = Ridic.idRidic
                                        AND Osoba.idOsoba = Ridic.idOsoba
                                        AND Ridic.idStav = Stav.idStav
                                        AND (Stav.nazev = 'volno'
                                        OR Stav.nazev = 'vedle')
                                        AND Jizda.idObjednavka IS NOT NULL
                                ORDER BY Jizda.casKonec ASC
                                )
                ";

        $result = $this->database->query($sql);
        $fronta = array();
        foreach ($result as $key => $val) {
            $fronta[$val->idRidic] = $val->prezdivka;
        }
        //ridici setrideni podle posledni objednavky kterou odjeli
        $sql = "SELECT DISTINCT Ridic.idRidic,prezdivka
                FROM
                    RidiciVeSluzbe,
                    Ridic,
                    Jizda,
                    Stav,
                    Osoba
                WHERE
                    Jizda.idRidic = RidiciVeSluzbe.idRidic
                        AND RidiciVeSluzbe.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Ridic.idStav = Stav.idStav
                        AND (Stav.nazev = 'volno'
                        OR Stav.nazev = 'vedle')
                        AND Jizda.idObjednavka IS NOT NULL
                ORDER BY Jizda.casKonec ASC
                LIMIT ?";

        $result = $this->database->query($sql, $this->dm->pocetVeSluzbe());
        foreach ($result as $key => $val) {
            $fronta[$val->idRidic] = $val->prezdivka;
        }
        $sql = "SELECT DISTINCT
                    Ridic.idRidic, prezdivka
                FROM
                    RidiciOstatniKDispozici,
                    Ridic,
                    Jizda,
                    Stav,
                    Osoba
                WHERE
                                RidiciOstatniKDispozici.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Ridic.idStav = Stav.idStav
                        AND (Stav.nazev = 'volno'
                        OR Stav.nazev = 'vedle')
                        AND Ridic.idRidic NOT IN(SELECT DISTINCT Ridic.idRidic
                                FROM
                                    RidiciOstatniKDispozici,
                                    Ridic,
                                    Jizda,
                                    Stav,
                                    Osoba
                                WHERE
                                    Jizda.idRidic = RidiciOstatniKDispozici.idRidic
                                        AND RidiciOstatniKDispozici.idRidic = Ridic.idRidic
                                        AND Osoba.idOsoba = Ridic.idOsoba
                                        AND Ridic.idStav = Stav.idStav
                                        AND (Stav.nazev = 'volno'
                                        OR Stav.nazev = 'vedle')
                                        AND Jizda.idObjednavka IS NOT NULL
                                ORDER BY Jizda.casKonec ASC
                                )
                ";

        $result = $this->database->query($sql);
      
        foreach ($result as $key => $val) {
            $fronta[$val->idRidic] = $val->prezdivka;
        }
        //ridici setrideni podle posledni objednavky kterou odjeli
        $sql = "SELECT DISTINCT Ridic.idRidic,prezdivka
                FROM
                    RidiciOstatniKDispozici,
                    Ridic,
                    Jizda,
                    Stav,
                    Osoba
                WHERE
                    Jizda.idRidic = RidiciOstatniKDispozici.idRidic
                        AND RidiciOstatniKDispozici.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Ridic.idStav = Stav.idStav
                        AND (Stav.nazev = 'volno'
                        OR Stav.nazev = 'vedle')
                        AND Jizda.idObjednavka IS NOT NULL
                ORDER BY Jizda.casKonec ASC
                LIMIT ?";

        $result = $this->database->query($sql, $this->dm->pocetNaKlouzani());
        foreach ($result as $key => $val) {
            $fronta[$val->idRidic] = $val->prezdivka;
        }



        //vytáhnu ridice mimo sluzbu a setridim je podle casu posledni objednavky, kterou dostali
        //pokud nema žádnou objednavku, rozhodne čas příchodu do práce
        //nyni jen pro pokusy zobrazim obyčejny seznam ridicu v pracu

        return $fronta;
    }

}
