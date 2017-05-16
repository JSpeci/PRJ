<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\model;

use Nette;

/**
 * Objekt resici prioritni frontu ridicu cekajicich na objednavku - sluzba/pohotovost/v aute
 */
class FrontaManager
{
    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getPublicArticles()
    {
        
    }
}