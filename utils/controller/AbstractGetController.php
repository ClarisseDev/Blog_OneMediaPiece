<?php

namespace OneMediaPiece_blog\utils\controller;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\controller\AbstractController;

abstract class AbstractGetController extends AbstractController implements IController
{
    protected int $id;
    protected mixed $response;
    protected IService $service;

    public function __construct(array $form, string $controllerName)
    {
        parent::__construct($form, $controllerName);
        $this->service = $this->getService();
    }

    //Tests si les paramètres sont bien présents
    protected function checkForm()
    {
        //error_log(__LINE__ . " " . __FUNCTION__);
        if (!isset($this->form['id']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter id not exists", 400);
        }
    }

    // Tester si les paramètres sont conformes
    protected function checkCybersec()
    {
        if(!Functions::isNaturalInteger($this->form['id']))
        {
            throw new HttpStatusException ("parameter id not a natural integer", 400);
        }
        $this->id = intval($this->form['id']);
    
    }

    abstract protected function processRequest();

    abstract protected function getService() : IService;


}

?>