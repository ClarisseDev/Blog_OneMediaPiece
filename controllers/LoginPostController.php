<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\logs\Logger;
use OneMediaPiece_blog\utils\logs\LOG_LEVEL;
use OneMediaPiece_blog\utils\SessionManager;
use OneMediaPiece_blog\utils\Functions;

class LoginPostController extends AbstractController implements IController
{
    private CompteService $service;
    private string $login;
    private string $password;
    private $currentSessionOwner;

    public function __construct(array $form)
    {
        parent::__construct($form,"LoginPostController");
        $this->service = new CompteService();
        $sessionInfo = SessionManager::getCompteIdFromSession();

        if ($sessionInfo === null) {
            Logger::logInErrorLog("Aucun compte_id trouvé dans la session.", LOG_LEVEL::WARN);
            $this->currentSessionOwner = null;
        } else {
            $this->currentSessionOwner = $this->service->findById($sessionInfo);
        }
    }

    protected function checkForm()       //Teste si paramètres bien présents
    {
        //error_log(__LINE__ . " " . __FUNCTION__);
        if (!isset($this->form['login']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter login not exists", 400);
        }
        if (!isset($this->form['password']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter password not exists", 400);
        }
    }

    protected function checkCybersec()     //Teste si paramètres conformes
    {
        if(!Functions::isEmail($this->form['login']))
        {
            throw new HttpStatusException ("parameter login is not a valid string", 400);
        }
        $this->login = Functions::sanitizeString($this->form['login']);

        if(!Functions::isSanitizedString($this->form['password']))
        {
            throw new HttpStatusException ("parameter password is not a valid string", 400);
        }
        $this->password = Functions::sanitizeString($this->form['password']);
    }

    protected function checkRights()
    {
        // error_log(__LINE__ . " " . __FUNCTION__);

    }

    // Gérer la connexion
    protected function processRequest()
    {
        if (Functions::isLogged())
        {
            throw new HttpStatusException("Already Authenticated", 499);
        }
        $compte = Compte::createForCredential($this->login, $this->password);
        $id = $this->service->isValidCredential($compte);
        if (is_null($id))
        {
            throw new HttpStatusException("Invalid Credential", 499);
        }
        SessionManager::login($id);
        error_log("Session après connexion: " . print_r($_SESSION, true)); 
        $this->response = [
            'success' => true,
            'message' => "Vous êtes connecté 🎉"
        ];
    }


}

?>