<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use app\model\SmenaManager;
use app\model\DochazkaManager;
use app\model\VypisManager;
use \app\model\JizdaManager;
use Nette\Utils\DateTime;

class HomepagePresenter extends Nette\Application\UI\Presenter {

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

    public function renderDefault() {

        $this->template->dispecer = $this->dm->dispeceriVPraci();

        $this->template->ridiciVeSluzbe = $this->dm->ridiciVeSluzbe();

        $this->template->ridiciVsichni = $this->vm->vsichniRidici();

        $this->template->ridiciKDispozici = $this->dm->ridiciKlouzani();

        $this->template->nikdoVeSluzbe = ($this->dm->pocetVeSluzbe() == 0);
        $this->template->nikdoNaKlouzani = ($this->dm->pocetNaKlouzani() == 0);
        $this->template->zadnyRidic = ($this->dm->pocetNaKlouzani() == 0 && $this->dm->pocetVeSluzbe() == 0);
        $this->template->zadnyDispecer = ($this->dm->pocetDispeceru() == 0);
        
        $this->template->prihlasen = $this->user->isLoggedIn();
    }

    protected function createComponentStavForm() {

        $stavy = $this->vm->stavyAsArray();
        $ridici = $this->dm->ridiciVPraciAsArray();

        $form = new Form(); // Nette\Application\UI\Form
        $form->addSelect('idRidic', 'Jméno:', $ridici)->setPrompt('Vyber řidiče')->setRequired();
        $form->addRadioList("stavNazev", "Stavy:", $stavy)->setDefaultValue($stavy['volno'])->setRequired();
        $form->addSubmit('nastav_stav', 'Nastavit stav');
        $form->onSuccess[] = [$this, 'stavFormSucceeded'];
        return $form;
    }

    public function stavFormSucceeded($form, $values) {

        $values = $form->getValues(true);
        $this->dm->nastavRidiceDoStavu($values['idRidic'], $values['stavNazev']);
        $this->redirect('this');
    }

    protected function createComponentDispecerStavForm() {

        $dispeceri = $this->dm->dispeceriVPraciAsArray();
        $moznosti = array();
        $moznosti["vedle"] = "vedle";
        $moznosti["pracuje"] = "pracuje";

        $form = new Form(); // Nette\Application\UI\Form
        $form->addSelect("idDispecer", "Vyber:", $dispeceri)->setRequired();
        $form->addRadioList("stavDispecer", "Vyber:", $moznosti)->setRequired();
        $form->addSubmit('dispecerStavSubmit', 'Nastav');
        $form->onSuccess[] = [$this, 'dispecerStavFormSucceeded'];
        return $form;
    }

    public function dispecerStavFormSucceeded($form, $values) {

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $values = $form->getValues(true);
        $this->dm->nastavDispeceraDoStavu($values['idDispecer'], $values['stavDispecer']);
        // $this->flashMessage('Děkuji za úpravu', 'success');
        $this->redirect('this');
    }


}
