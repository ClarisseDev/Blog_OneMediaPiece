<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\services\RoleService;
use OneMediaPiece_blog\utils\controller\AbstractPostController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\Functions;

class RolePostController extends AbstractPostController implements IController
{
    protected array $form;
    protected int $id;
    protected mixed $response;

    public function __construct(array $form)
    {
        $this->form = $form;
    }

    function execute() : string
    {
        $this->checkForm();         //il existe ?
        $this->checkCybersec();     // il est conforme ?
        $this->checkRights();
        $this->processRequest();
        return $this->processResponse();
    }

    //Tests si paramètres bien présents
    protected function checkForm()
    {
        //error_log(__LINE__ . " " . __FUNCTION__);
        if (!isset($this->form['label']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter label not exists" . __CLASS__ . __FUNCTION__ . __LINE__, 400);
        }
    }

    protected function checkCybersec()
    {
        if(!Functions::isNaturalInteger($this->form['label']))
        {
            throw new HttpStatusException ("parameter label not exists" . __CLASS__ . __FUNCTION__ . __LINE__, 400);
        }
        $this->id = intval($this->form['label']);
    
    }

    protected function checkRights(){error_log(__LINE__ . " " . __FUNCTION__);}

    protected function processRequest()
    {
        $this->response = $this->getService()->insert($this->form['label']);
    }

    protected function processResponse()
    {
       if (is_null($this->response))
       {
        error_log("Unable to find something");          // TODO Faire une méthode abstraite
        throw new HttpStatusException("", 404);
       }
       $output = json_encode($this->response);
       $cleanedOutput = ltrim ($output);         // suppression des espaces avant et après
       return $cleanedOutput;
    }
}

?>