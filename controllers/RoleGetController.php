<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\services\RoleService;
use OneMediaPiece_blog\utils\controller\AbstractGetController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\service\IService;

class RoleGetController extends AbstractGetController  implements IController
{

    public function __construct(array $form)
    {
        parent::__construct($form);
    }

    protected function getService() : IService {
        return $this->service;
    }

    protected function processRequest()
    {
        // Appeler le service pour récupérer tous les rôles
        $roles = $this->service->findAll();

        // Retourner les rôles sous forme de tableau JSON
        $this->response = json_encode($roles);
    }



}

?>