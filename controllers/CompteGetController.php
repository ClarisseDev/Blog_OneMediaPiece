<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\controller\AbstractGetController;
use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use OneMediaPiece_blog\exceptions\HttpStatusException;


class CompteGetController extends AbstractGetController implements IController
{
    private $currentSessionOwner;

    public function __construct(array $form)
    {
        parent::__construct($form, "CompteGetController");
        $this->service = new CompteService();
        $sessionInfo = SessionManager::getCompteIdFromSession();
        $this->currentSessionOwner = $this->compteService->findById($sessionInfo);
    }

    protected function getService() : IService {
        return $this->service;
    }

    protected function checkRights() {
        if (!Functions::isLogged()) {
            throw HttpStatusException::_401_Unauthorized();
        }
        
        // Vérifie que l'utilisateur peut voir ce compte
        $currentUserId = SessionManager::getCompteIdFromSession();
        if ($this->id !== $currentUserId) {
            throw HttpStatusException::_403_Forbidden("You can only view your own account");
        }
    }

    protected function processRequest()
    {
        $this->response = $this->getService()->findById($this->id);
    }
}

?>