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
use \app\model\ObjednavaManager;
use app\model\FrontaRidicuManager;
use Nette\Utils\DateTime;

/**
 * Description of ObjednavkaPresenter
 *
 * @author Jan Špecián
 */
class ObjednavkaPresenter extends \Nette\Application\UI\Presenter {

    /** @var Nette\Database\Context */
    private $database;
    private $jm;
    private $dm;
    private $am;
    private $om;
    private $fm;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->jm = new JizdaManager($database);
        $this->dm = new DochazkaManager($database);
        $this->am = new AdresaManager($database);
        $this->om = new ObjednavaManager($database);
        $this->fm = new FrontaRidicuManager($database);
    }

    public function renderVytvorit() {
        
    }

    public function renderNevyrizene() {
        $this->template->nevyrizeneObjednavky = $this->om->nevyrizeneObjednavky();
        $this->template->frontaRidicu = $this->fm->getAktualniFrontaRidicu();
    }

    protected function createComponentVyriditObjednavkuForm() {

        $form = new Form(); // means Nette\Application\UI\Form
        //Z OBJEDNAVKY SE STANE JIZDA
        
        
        //vybrat kterou objednavku
        $form->addGroup("Objednávka");
        $result = $this->om->nevyrizeneObjednavky();
        $objednavky = array();
        foreach ($result as $key => $value) {
            $objednavky[$value->idObjednavka] = $value->cas. " ". $value->ulice. " ".$value->cislo." ". $value->mesto;
        }
        $form->addSelect("idObjednavka", "Vyber Objednávku", $objednavky);
        
        //--------ADRESA---------------------------------------

        $form->addGroup("Kam pojedou");
        
        $form->addText("ulice", "Jméno ulice")->setRequired();

        $form->addText("cislo", "Číslo popisné")->setType('number')->setRequired()
                ->addRule(Form::RANGE, 'Číslo od %d do %d', [0, 5000])->setValue(1);

        $form->addText("mesto", "Město")->setDefaultValue("Liberec")->setRequired();
        
        $form->addGroup("Kdo pojede");
        
        $form->addSelect("idRidic", "Kdo vyřídí jízdu", $this->fm->getAktualniFrontaRidicu());

        $form->addSubmit('objednavkaVlozitSubmit', 'Vložit');

        $form->onSuccess[] = [$this, 'vyriditObjednavkuFormSucceeded'];


        return $form;
    }

    public function vyriditObjednavkuFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $casTed = DateTime::from(time());

        //pokud neznam adresu, vytvorim ji
        if (!$this->am->znamTutoAdresu($values['ulice'], $values['cislo'], $values['mesto'])) {
            $this->am->novaAdresa($values['ulice'], $values['cislo'], $values['mesto']);
        }

        $idAdresa_kam = $this->am->getAdresaByUliceCisloMesto($values['ulice'], $values['cislo'], $values['mesto'])->fetch()['idAdresa'];
        $idAdresa_od = $this->om->getIdAdresa($values['idObjednavka']);

        $this->jm->novaJizda($values['idRidic'], $idAdresa_od, $idAdresa_kam, $casTed, $values['idObjednavka']);
        $this->om->vyriditObjednavku($values['idObjednavka']);

        $this->flashMessage("Objednavka předána", 'success');
        $this->redirect('Objednavka:nevyrizene');
    }

    protected function createComponentVytvoritObjednavkuForm() {

        $form = new Form(); // means Nette\Application\UI\Form
        //--------ADRESA---------------------------------------

        $form->addText("ulice", "Jméno ulice")->setRequired();

        $form->addText("cislo", "Číslo popisné")->setType('number')->setRequired()
                ->addRule(Form::RANGE, 'Číslo od %d do %d', [0, 5000])->setValue(1);

        $form->addText("mesto", "Město")->setDefaultValue("Liberec")->setRequired();

        $form->addText("hodina", "Hodin")->setType('number')
                ->addRule(Form::RANGE, 'Hodin musí být od %d do %d', [0, 24])->setValue(date("H"))->setRequired();

        $form->addText("minuta", "Minut")->setType('number')
                ->addRule(Form::RANGE, 'Minut musí být od %d do %d', [0, 59])->setValue(date("i"))->setRequired();

        $form->addText("den", "Den")->setType('number')
                ->addRule(Form::RANGE, 'Den musí být od %d do %d', [1, 31])->setValue(date("d"))->setRequired();

        $form->addText("mesic", "Měsíc")->setType('number')
                ->addRule(Form::RANGE, 'Měsíc musí být od %d do %d', [1, 12])->setValue(date("m"))->setRequired();

        $form->addText("rok", "Rok")->setType('number')
                ->addRule(Form::RANGE, 'Rok musí být od %d do %d', [2017, 2050])->setValue(date("Y"))->setRequired();

        $form->addText("poznamka", "Poznámka");

        $form->addSubmit('objednavkaVlozitSubmit', 'Vložit');

        $form->onSuccess[] = [$this, 'vytvoritObjednavkuFormSucceeded'];


        return $form;
    }

    public function vytvoritObjednavkuFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $casPristaveniVozu = DateTime::fromParts($values['rok'], $values['mesic'], $values['den'], $values['hodina'], $values['minuta'], 0);

        if (!$this->am->znamTutoAdresu($values['ulice'], $values['cislo'], $values['mesto'])) {
            $this->am->novaAdresa($values['ulice'], $values['cislo'], $values['mesto']);
        }

        $idAdresa = $this->am->getAdresaByUliceCisloMesto($values['ulice'], $values['cislo'], $values['mesto'])->fetch()['idAdresa'];

        $this->om->vytvoritObjednavku($casPristaveniVozu, $idAdresa);

        $this->flashMessage("Objednavka vložena", 'success');
        $this->redirect('Homepage:');
    }

}
