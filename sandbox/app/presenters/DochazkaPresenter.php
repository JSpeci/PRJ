<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use app\model\SmenaManager;
use app\model\DochazkaManager;
use app\model\VypisManager;
use Nette\Utils\DateTime;

/**
 * Description of DochazkaPresenter
 *
 * @author King
 */
class DochazkaPresenter extends Nette\Application\UI\Presenter {

    /** @var Nette\Database\Context */
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
    
    public function renderDochazka()
    {
             
        //posledni směna
        $this->template->posledniSmenaBezi = $this->sm->posledniSmenaTrva();
        $this->template->aktualniSmena = $this->sm->posledniSmena();
        $this->template->osobyVeSmene = $this->sm->osobyVPosledniSmene();
    }
    
    protected function createComponentSmenaVytvoritForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $form->addText("hodinaOd", "Začátek směny")->setType('number')
                ->addRule(Form::RANGE, 'Hodina musí být od %d do %d', [0, 24])->setValue(8)->setRequired();

        $form->addText("hodinTrvani", "Trvání hodin")->setType('number')
                ->addRule(Form::RANGE, 'Hodin musí být od %d do %d', [0, 24])->setValue(12)->setRequired();

        $form->addText("den", "Den")->setType('number')
                ->addRule(Form::RANGE, 'Den musí být od %d do %d', [1, 31])->setValue(date("d"))->setRequired();

        $form->addText("mesic", "Měsíc")->setType('number')
                ->addRule(Form::RANGE, 'Měsíc musí být od %d do %d', [1, 12])->setValue(date("m"))->setRequired();

        $form->addText("rok", "Rok")->setType('number')
                ->addRule(Form::RANGE, 'Rok musí být od %d do %d', [2017, 2050])->setValue(date("Y"))->setRequired();

        $form->addSubmit('nastav_smenu', 'Vytvořit směnu');

        $form->onSuccess[] = [$this, 'smenaVytvoritFormSucceeded'];

        return $form;
    }

    public function smenaVytvoritFormSucceeded($form, $values) {

        $values = $form->getValues(true);
        $od = DateTime::fromParts($values['rok'], $values['mesic'], $values['den'], $values['hodinaOd']);
        $do = DateTime::from($od->modify('+' . $values['hodinTrvani'] . 'hours'));
        $od = DateTime::fromParts($values['rok'], $values['mesic'], $values['den'], $values['hodinaOd']);

        $this->sm->createSmena($od, $do);
        $this->flashMessage($od . "  " . $do, 'success');
        $this->redirect('this');
    }

    protected function createComponentDochazkaRidicuForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $ridici = $this->vm->vsichniRidiciAsArray();

        $form->addSelect('ridic', 'Jméno:', $ridici)->setPrompt('Vyber řidiče')->setRequired();

        $moznosti = array();
        $moznosti[0] = "Příchod";
        $moznosti[1] = "Odchod";
        $form->addRadioList("akce", "Akce:", $moznosti)->setDefaultValue(0)->setRequired();

        $moznosti2 = array();
        $moznosti2[0] = "Do služby";
        $moznosti2[1] = "Klouzání";

        $form->addRadioList("role", "Role:", $moznosti2)->setDefaultValue(0)->setRequired();
        $form->addSubmit('dochazkaSubmit', 'Zadej');
        $form->onSuccess[] = [$this, 'dochazkaRidicuFormSucceeded'];
        return $form;
    }

    public function dochazkaRidicuFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $casTed = DateTime::from(time());

        if ($values['akce'] == 0) { //prichod
            if ($values['role'] == 0) { //sluzba
                $this->dm->prichodRidicSluzba($values['ridic'], $casTed);
            }

            if ($values['role'] == 1) { //klouzak
                $this->dm->prichodRidicKlouzani($values['ridic'], $casTed);
            }
        }

        if ($values['akce'] == 1) { //odchod
            $this->dm->odchodRidice($values['ridic'], $casTed);
        }

        $this->flashMessage("", 'success');
        $this->redirect('this');
    }

    protected function createComponentDochazkaDispeceruForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $dispeceri = $this->vm->vsichniDispeceriAsArray();

        $form->addSelect('dispecer', 'Jméno:', $dispeceri)->setPrompt('Vyber dispecera')->setRequired();
        ;

        $moznosti = array();
        $moznosti[0] = "Příchod";
        $moznosti[1] = "Odchod";
        $form->addRadioList("akce", "Akce:", $moznosti)->setDefaultValue(0)->setRequired();

        $form->addSubmit('dochazkaSubmit', 'Zadej');

        $form->onSuccess[] = [$this, 'dochazkaDispeceruFormSucceeded'];

        return $form;
    }

    public function dochazkaDispeceruFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $casTed = DateTime::from(time());

        if ($values['akce'] == 0) { //prichod
            $this->dm->prichodDispecer($values['dispecer'], $casTed);
        }

        if ($values['akce'] == 1) { //odchod
            $this->dm->odchodDispecer($values['dispecer'], $casTed);
            $this->user->logout();
        }

        $this->flashMessage("", 'success');
        $this->redirect('this');
    }

    protected function createComponentSmenaPridavaniLidiForm() {

        $form = new Form(); // means Nette\Application\UI\Form

        $result = $this->sm->osobyKtereLzeVlozitDoPosledniSmeny();
        $osoby = array();
        foreach ($result as $key => $value) {
            $osoby[$value['idOsoba']] = $value['prezdivka'];
        }

        $form->addSelect("idOsoba", "Vyber řidiče", $osoby);

        $form->addSubmit('smenaVlozitSubmit', 'Vložit');

        $form->onSuccess[] = [$this, 'smenaPridavaniLidiFormSucceeded'];

        return $form;
    }

    public function smenaPridavaniLidiFormSucceeded($form, $values) {

        $values = $form->getValues(true);

        $this->sm->pridejOsobuDoPosledniSmeny($values['idOsoba']);

        //$this->flashMessage("", 'success');
        $this->redirect('this');
    }

}
