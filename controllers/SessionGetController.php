<?php

namespace OneMediaPiece_blog\controllers;

use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\SessionManager;
use stdClass;


class SessionGetController extends AbstractController implements IController
{
    protected string $controllerName;

    public function __construct(array $form, string $controllerName)
    {
        $this->controllerName = $controllerName;
    }

    protected function checkForm() { /* */ }

    protected function checkCybersec() { /* */ }
		
	protected function checkRights() { /* */ }

    protected function processRequest() {
			$state = array();
			$sessionManager = SessionManager::getInstance();
			$state['isLogged'] = $sessionManager->isLogged();
			$state['startTime'] = date("Y-m-d H:i:s", $sessionManager->getStartTime());
			$state['timeOut'] = date("Y-m-d H:i:s", $sessionManager->getTimeOut());
			if ($sessionManager->isLogged()) {
				/** @var Compte $compte */
				$compte = $this->compteService->findById($sessionManager->getCompteIdFromSession());
				$state['info'] = [
					"pseudo" => $compte->getPseudo(),
					"role" => $compte->getRole()->getLabel(),
					"roleId" => $compte->getRole()->getPrimaryKey()
				];
			}
			$this->response = $state;
		}

    public function getName(): string {
        return $this->controllerName;
    }

}

?>