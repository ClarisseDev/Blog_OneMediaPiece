<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\services\RoleService;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use OneMediaPiece_blog\utils\IService;
use DateTime;


class ComptePutController extends AbstractController implements IController
{
    private CompteService $service;
    private int $idCompte;
    private string $login;
    private string $password;
    private string $pseudo;
    private bool $enAttenteDeModeration;
    private bool $estSupprime;
    private bool $estSignale;
    private bool $estBanni;
    private $currentSessionOwner;

    public function __construct(array $form)
    {
        parent::__construct($form);
        $this->service = new CompteService();
        $sessionInfo = SessionManager::getCompteIdFromSession();
        $this->currentSessionOwner = $this->compteService->findById($sessionInfo);
    }

    // Vérifier si les paramètres du formulaire sont présents
    protected function checkForm()       
    {
        //error_log(__LINE__ . " " . __FUNCTION__);
        if (!isset($this->form['id_compte']))     // si le paramètre n'existe pas, 400
        {
            throw new HttpStatusException ("parameter id_compte not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['login']))     
        {
            throw new HttpStatusException ("parameter login not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['password']))     
        {
            throw new HttpStatusException ("parameter password not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['pseudo']))     
        {
            throw new HttpStatusException ("parameter pseudo not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['dateCreation']))     
        {
            throw new HttpStatusException ("parameter dateCreation not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['dateModification']))     
        {
            throw new HttpStatusException ("parameter dateModification not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['enAttenteDeModeration']))     
        {
            throw new HttpStatusException ("parameter enAttenteDeModeration not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['estSupprime']))     
        {
            throw new HttpStatusException ("parameter estSupprime not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['estSignale']))     
        {
            throw new HttpStatusException ("parameter estSignale not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

        if (!isset($this->form['estBanni']))     
        {
            throw new HttpStatusException ("parameter estBanni not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        
        if (!isset($this->form['fk_role']))     
        {
            throw new HttpStatusException ("parameter fk_role not exists : " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }

    }

    // Verifier la conformité des données du formulaire
    protected function checkCybersec()
    {
        if (!Functions::isNaturalInteger($this->form['id_compte']))    
        {
            throw new HttpStatusException("parameter id_compte is not a natural integer : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400); // Si le paramètre n'est pas conforme, retourne une erreur 400
        }
        $this->idCompte = intval($this->form['id_compte']);

        if (!Functions::isEmail($this->form['login'])) {
            throw new HttpStatusException("parameter login is not a valid string : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->login = Functions::sanitizeString($this->form['login']);

        if (!Functions::isPassword($this->form['password'])) {
            throw new HttpStatusException("parameter password is not a valid password : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->password = Functions::sanitizeString($this->form['password']);

        if (!Functions::isSanitizedString($this->form['pseudo'])) {
            throw new HttpStatusException("parameter pseudo is not a valid string : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->pseudo = Functions::sanitizeString($this->form['pseudo']);

        if (!Functions::isSanitizedString($this->form['enAttenteDeModeration']))     
        {
            throw new HttpStatusException ("parameter enAttenteDeModeration is not a boolean : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->enAttenteDeModeration = Functions::stringToBool($this->form['enAttenteDeModeration']);

        if (!Functions::isSanitizedString($this->form['estSupprime'])) 
        {
            throw new HttpStatusException ("parameter estSupprime is not a boolean : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->estSupprime = Functions::stringToBool($this->form['estSupprime']);

        if (!Functions::isSanitizedString($this->form['estSignale']))  
        {
            throw new HttpStatusException ("parameter estSignale is not a boolean : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->estSignale = Functions::stringToBool($this->form['estSignale']);

        if (!Functions::isSanitizedString($this->form['estBanni'])) 
        {
            throw new HttpStatusException ("parameter estBanni is not a boolean : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        $this->estBanni = Functions::stringToBool($this->form['estBanni']);

        if (!Functions::isNaturalInteger($this->form['fk_role']))    
        {
            throw new HttpStatusException ("parameter fk_role is not a natural integer : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
    }

    protected function checkRights()
    {
        // error_log(__LINE__ . " " . __FUNCTION__);

        // Vérifie si l'utilisateur connecté a un rôle suffisant
        if ($_SESSION['roleId'] == 1) 
        {
            throw new HttpStatusException("You don't have rights to update : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 403);
        }
    }

    protected function processRequest()
    {
        // Vérifier si on a déjà une session active
        if (!Functions::isLogged()) 
        {
            throw new HttpStatusException("Not Authenticated : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 499);
        }

        // Récupère les informations du rôle et on les met dans un objet Role
        $roleService = new RoleService();
        $role = $roleService->findById($this->form['fk_role']);

        // Créez un objet Compte à partir des données du formulaire
        $compte = new Compte();
        $compte->setIdCompte(intval($this->form['id_compte']));
        $compte->setLogin($this->form['login']);
        $compte->setPassword($this->form['password']); // Assurez-vous que le mot de passe est correctement manipulé
        $compte->setPseudo($this->form['pseudo']);
        $compte->setDateCreation(new DateTime($this->form['dateCreation']));
        $compte->setDateModification(new DateTime($this->form['dateModification']));
        $compte->setEnAttenteDeModeration(Functions::stringToBool($this->form['enAttenteDeModeration']));
        $compte->setEstSupprime(Functions::stringToBool($this->form['estSupprime']));
        $compte->setEstSignale(Functions::stringToBool($this->form['estSignale']));
        $compte->setEstBanni(Functions::stringToBool($this->form['estBanni']));
        $compte->setRole($role);

        // Fournissez l'objet Compte au service
        $rowCount = $this->service->update($compte);

        // Réponse
        $this->response = $rowCount;
    }


}

?>