<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use app\model\SmenaManager;
use app\model\DochazkaManager;
use app\model\VypisManager;
use \app\model\JizdaManager;
use app\model\AdresaManager;
use Nette\Utils\DateTime;

/**
 * @author Jan Špecián
 */
class JizdaPresenter extends \Nette\Application\UI\Presenter {

    /** @var Nette\Database\Context */
    private $database;
    private $jm;
    private $dm;
    private $am;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->jm = new JizdaManager($database);
        $this->dm = new DochazkaManager($database);
        $this->am = new AdresaManager($database);
    }

    public function renderVsechny($idRidic) {
        $this->template->jizdy = $this->jm->vsechnyJizdyRidice($idRidic, 50);
    }

    public function renderJizdy() {
        $this->template->probihajiciJizdy = $this->jm->probihajiciJizdy();
        $this->template->vsechnaMista = $this->am->vsechnaMista();
    }

    protected function createComponentJizdaForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $mista = $this->am->vsechnaMistaAsArray();
        $volby = array();
        $volby["misto"] = "misto";
        $volby["adresa"] = "adresa";

        //--------OD---------------------------------------
        $form->addGroup("odkud");


        $form->addText("uliceOd", "Jméno ulice")->setRequired();

        $form->addText("cisloOd", "Číslo popisné")->setType('number')->setRequired()
                ->addRule(Form::RANGE, 'Číslo od %d do %d', [0, 5000])->setValue(1);

        $form->addText("mestoOd", "Město")->setDefaultValue("Liberec")->setRequired();

        //--------KAM---------------------------------------
        $form->addGroup("kam");


        $form->addText("uliceKam", "Jméno ulice")->setRequired();

        $form->addText("cisloKam", "Číslo popisné")->setType('number')->setRequired()
                ->addRule(Form::RANGE, 'Číslo od %d do %d', [0, 5000])->setValue(1);

        $form->addText("mestoKam", "Město")->setDefaultValue("Liberec")->setRequired();

        $form->addGroup("kdo");
        $ridici = $this->dm->ridiciVPraciAsArray();
        $form->addSelect("idRidic", "Řidič", $ridici)->setRequired();
        //-------------------------------------------------

        $form->addSubmit('jizdaVlozitSubmit', 'Vložit');

        $form->onSuccess[] = [$this, 'jizdaFormSucceeded'];


        return $form;
    }

    public function jizdaFormSucceeded($form, $values) {

        $values = $form->getValues(true);


        $casTed = DateTime::from(time()); //nyni

        $form->getComponent("uliceOd")->setRequired();
        if (!$this->am->znamTutoAdresu($values['uliceOd'], $values['cisloOd'], $values['mestoOd'])) {   //neznam - vlozim novou adresu
            $this->am->novaAdresa($values['uliceOd'], $values['cisloOd'], $values['mestoOd']);
        }
        $idAdresa_od = $this->am->getAdresaByUliceCisloMesto($values['uliceOd'], $values['cisloOd'], $values['mestoOd'])->fetch()['idAdresa'];



        if (!$this->am->znamTutoAdresu($values['uliceKam'], $values['cisloKam'], $values['mestoKam'])) {   //neznam - vlozim novou adresu
            $this->am->novaAdresa($values['uliceKam'], $values['cisloKam'], $values['mestoKam']);
        }
        $idAdresa_kam = $this->am->getAdresaByUliceCisloMesto($values['uliceKam'], $values['cisloKam'], $values['mestoKam'])->fetch()['idAdresa'];


        $this->jm->novaJizda($values['idRidic'], $idAdresa_od, $idAdresa_kam, $casTed);




        $this->flashMessage("Jízda vložena", 'success');
        $this->redirect('this');
    }

    protected function createComponentDokonceniJizdyForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $res = $this->jm->probihajiciJizdy();
        $items = array();
        foreach ($res as $key => $val) {
            $items[$val->idJizda] = $val->prezdivka . " z " .
                    $val->uliceOd . " " .
                    $val->cisloOd . " do " .
                    $val->uliceDo . " " .
                    $val->cisloDo;
        }

        $form->addRadioList("idJizda", "", $items)->setRequired();

        $form->addSubmit('jizdaDokoncittSubmit', 'Dojel jízdu');

        $form->onSuccess[] = [$this, 'dokonceniJizdyFormSucceeded'];

        return $form;
    }

    public function dokonceniJizdyFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $casTed = DateTime::from(time()); //nyni

        $this->jm->konecProbihajiciJizdy($values['idJizda'], $casTed);

        $this->redirect('this');
    }

}
