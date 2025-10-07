<?php

namespace OneMediaPiece_blog\utils\controller;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\services\CompteService;
use Throwable;
use Exception;

abstract class AbstractController implements IController
{
    protected array $form;
    protected mixed $response;
	protected string $controllerName;
    protected CompteService $compteService;

    public function __construct(array $form, string $controllerName)
    {
        $this->form = $form;
        $this->controllerName = $controllerName;
        $this->compteService = new CompteService();
    }

    function execute() : string
    {
        $this->checkForm();        
        $this->checkCybersec();     
        $this->checkRights();
        $this->processRequest();
        return $this->processResponse();
    }

    protected abstract function checkForm();        //Teste si paramètres bien présents

    protected abstract function checkCybersec();     //Teste si paramètres conformes

    protected function checkRights()
    {// error_log(__LINE__ . " " . __FUNCTION__);
    }

    protected abstract function processRequest();

    protected function processResponse()
    {
       if (is_null($this->response))
       {
        error_log("Unable to find something");          // TODO Faire une méthode abstraite
        throw new HttpStatusException("", 404);
       }
       error_log("Response: " . print_r($this->response, true));

       $output = json_encode($this->response);
       $cleanedOutput = ltrim ($output);         // suppression des espaces avant et après
       return $cleanedOutput;
    }

    public function getName(): string {
        return $this->controllerName;
    }
}

?>