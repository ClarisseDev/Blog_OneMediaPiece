<?php

namespace OneMediaPiece_blog\utils\controller;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\controller\AbstractController;

abstract class AbstractPostController extends AbstractController implements IController
{
    protected int $id;
    protected mixed $response;
    protected IService $service;

    public function __construct(array $form)
    {
        parent::__construct($form);
        $this->service = $this->getService();
    }

    //Tests si paramètres bien présents
    protected function checkForm()
    {
        error_log(__CLASS__ . " " . __FUNCTION__ . " " .  __LINE__);
    }

    // Tester si les paramètres sont conformes
    protected function checkCybersec()
    {
        error_log(__CLASS__ . " " . __FUNCTION__ . " " .  __LINE__);
    }

    protected function getService() : IService {
        return $this->service;
    }

    protected function processRequest()
    {
        $this->response = $this->getService()->findById($this->id);
    }

}

?>