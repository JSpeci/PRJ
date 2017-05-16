<?php

namespace App\Presenters;

use Nette;

/**
 * @author Jan Špecián
 */
class RidicPresenter extends Nette\Application\UI\Presenter {

    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function renderVsichni() {
       
        $sql = "SELECT 
                    Osoba.prezdivka,
                    Stav.nazev AS 'stav',
                    Osoba.idOsoba AS 'idOsoba'
                FROM
                    Ridic,
                    Osoba,
                    Stav
                WHERE
                    Ridic.idRidic = Ridic.idRidic
                        AND Osoba.idOsoba = Ridic.idOsoba
                        AND Stav.idStav = Ridic.idStav";
        $this->template->ridici = $this->database->query($sql);
    }

}
