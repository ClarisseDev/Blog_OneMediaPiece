<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use OneMediaPiece_blog\exceptions\SqlConstraintUniqueException;
use Exception;

class ComptePostController extends AbstractController implements IController
{
    private CompteService $service;
    private string $login;
    private string $password;
    private string $pseudo;
    private $currentSessionOwner;

    public function __construct(array $form)
    {
        parent::__construct($form, "ComptePostController"); // Ajoutez le nom du contrôleur
        $this->service = new CompteService();
        $sessionInfo = SessionManager::getCompteIdFromSession();
        $this->currentSessionOwner = $this->compteService->findById($sessionInfo);
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
        if (!isset($this->form['pseudo']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter pseudo not exists", 400);
        }
    }

    protected function checkCybersec()
    {
        if (!Functions::isEmail($this->form['login'])) {
            throw new HttpStatusException("parameter login is not a valid string", 400);
        }
        $this->login = Functions::sanitizeString($this->form['login']);
    
        if (!Functions::isPassword($this->form['password'])) {
            throw new HttpStatusException("parameter password is not a valid password", 400);
        }
        $this->password = Functions::sanitizeString($this->form['password']);
    
        if (!Functions::isSanitizedString($this->form['pseudo'])) {
            throw new HttpStatusException("parameter pseudo is not a valid string", 400);
        }
        $this->pseudo = Functions::sanitizeString($this->form['pseudo']);
    }

    protected function checkRights()
    {
        // Vérifier si on a déjà une session active
        if (Functions::isLogged())
        {
            throw new HttpStatusException("Already Authenticated", 499);
        }
    }

    protected function processRequest()
    {
        error_log("Création du compte avec login: " . $this->login);
        $compte = Compte::createForCredential($this->login, $this->password, $this->pseudo);

        try {
            $idCompte = $this->service->insert($compte);
            error_log("Compte inséré avec l'ID: " . $idCompte);
            $this->response = [
                "status" => "success",
                "message" => "Compte créé avec succès",
                "id_compte" => $idCompte
            ];
        } catch (SqlConstraintUniqueException $ex) {
            error_log("Erreur de contrainte unique: " . $ex->getMessage());
            throw HttpStatusException::_498_Business_Error($ex);
        } catch (Exception $ex) {
            error_log("Erreur inattendue: " . $ex->getMessage());
            throw new HttpStatusException("Erreur inattendue lors de la création du compte", 500, $ex);
        }
    }


}

?>