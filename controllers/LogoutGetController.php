<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\Functions;
use Exception;
use OneMediaPiece_blog\utils\SessionManager;
use SessionHandler;

class LogoutGetController extends AbstractController implements IController
{

    public function __construct(array $form)
    {
        parent::__construct($form, "LogoutGetController");
    }

    protected function checkForm() {} // Pas de paramètres à vérifier

    protected function checkCybersec() {} // Pas de vérification de sécurité

    protected function checkRights() {} // Pas de vérification de droits

    protected function processRequest()
    {
        SessionManager::reinitSession();
        $this->response = ["status" => "success", "message" => "Déconnexion réussie"];
    }

    // Optionnel : Redéfinir processResponse pour s'assurer que la réponse est bien envoyée
    protected function processResponse()
    {
        header('Content-Type: application/json');
        return json_encode($this->response);
    }


}

?>