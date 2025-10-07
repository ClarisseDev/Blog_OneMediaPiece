<?php

namespace OneMediaPiece_blog\services;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\model\Role;
use OneMediaPiece_blog\daos\CompteDao;
use OneMediaPiece_blog\services\RoleService;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\service\AbstractService;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use PDOException;
use InvalidArgumentException;
use PDO;
use DateTime;
use Exception;

class CompteService extends AbstractService implements IService
{
    protected CompteDao $dao;
    protected RoleService $roleService;

    public function __construct()
    {
        $this->dao = new CompteDao();
        $this->roleService = new RoleService();
    }

    function getDao() : IDao
    {
        return $this->dao;
    }

    // CREATE
    function isValidCredential(Compte $compte) : ?int
    {
        return $this->dao->isValidCredential($compte);
    }


    public function insert(IEntity $entity): int
    {
        /** @var Compte $entity */
        error_log("Insertion d'un nouveau compte avec login: " . $entity->getLogin());
        // Règle métier n°1 : par défaut un compte est de rôle rédacteur (pk = 1)
        $role = $this->roleService->findById(1);
        /** @var Role $role */
        error_log("Rôle récupéré: " . $role->getLabel());
        // Règle métier n°2 : par défaut un compte passe en attente de modération
        $entity->setEnAttenteDeModeration(true);
        $entity->setRole($role);
        $date = new DateTime();
        $entity->setDateCreation($date);
        $entity->setDateModification($date);
        $entity->setEstSupprime(false);
        $entity->setEstSignale(false);
        $entity->setEstBanni(false);
        try {
            $idCompte = parent::insert($entity);
            error_log("Compte inséré avec l'ID: " . $idCompte);
            return $idCompte;
        } catch (Exception $ex) {
            error_log("Erreur lors de l'insertion du compte: " . $ex->getMessage());
            throw $ex;
        }
    }

    // RETREIVE
    function findById(int $id) : IEntity
    {
        // Si l'utilisateur est le propriétaire du compte ou un admin, on retourne toutes les infos
        if(SessionManager::isLogged() && (SessionManager::getCompteIdFromSession() == $id || SessionManager::getRoleIdFromSession() == 3))
        {
            return $this->getDao()->findById($id);
        }
        else
        {
            throw new HttpStatusException("Unauthorized to access this resource : " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 401);
        }
    }

    public function findByIdForSession(int $id): Compte
    {
        return $this->getDao()->findById($id);
    }

    function findByIdForLogin(int $id): IEntity
    {
        return $this->getDao()->findById($id);
    }

    // UPDATE
    function update (IEntity $entity) 
    {
        // Vérifier que le paramètre $entity est un objet type Compte
        if(!($entity instanceof Compte))
        {
            throw new InvalidArgumentException("Expected instance of Compte "  . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__);
        }
        
        // Tenir compte des règles métier : droits et role
        // INTERNAUTE (non connecté)
        if(!Functions::isLogged() || SessionManager::getCompteIdFromSession() == NULL)
        {
            throw new HttpStatusException("Unlogged -> Unauthorized to update : " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 401);
        }

        // On crée un objet qui représente la personne connectée
        $currentUser = $this->getDao()->findById(SessionManager::getCompteIdFromSession());
        /** @var Compte $currentUser */

        // REDACTEUR
        // Si la personne connectée est un Rédacteur dont l'Id est en SESSION
        // Règle métier : Peut modifier son propre mot de passe
        if(($currentUser->getRole()->getIdRole() == 1) && ($entity->getIdCompte() == $currentUser->getIdCompte()))
        {
            $currentUser->setPassword($entity->getPassword());
            return $this->getDao()->update($currentUser);
        }
        // MODO
        // Si la personne connectée est un Modérateur dont l'Id est en SESSION
        if(($currentUser->getRole()->getIdRole() == 2))
        {
            // Règle métier n°1 : Peut modifier l'attribut "estSignale" d'un autre compte
            // On récupère l'objet à modifier enregistré dans la BDD
            $oldEntity = $this->getDao()->findById(($entity->getIdCompte()));
            // $entity = EF et $oldEntity = EB
            /** @var Compte $oldEntity */
            // Mise à jour du "estSignale"
            $oldEntity->setEstSignale($entity->getEstSignale());
            // Règle métier n°2 : Peut modifier le password de son propre compte
            if($entity->getIdCompte() == $currentUser->getIdCompte())
            {
                $oldEntity->setPassword($entity->getPassword());
            }
            return $this->getDao()->update($oldEntity);
        }
        // ADMIN
        // Si la personne connectée est un Admin dont l'Id est en SESSION
        if(($currentUser->getRole()->getIdRole() == 3))
        {
            // On récupère l'objet à modifier enregistré dans la BDD
            $oldEntity = $this->getDao()->findById(($entity->getIdCompte()));
            
            // $entity = EF et $oldEntity = EB
            /** @var Compte $oldEntity */
            // Mise à jour selon les droits admin
            // Règle métier n°1 : Peut modifier les attributs "estSupprime",  "estBanni", "enAttenteDeModeration", "Role" d'un autre compte
            $oldEntity->setEstSupprime($entity->getEstSupprime());
            $oldEntity->setEstBanni($entity->getEstBanni());
            $oldEntity->setEnAttenteDeModeration($entity->getEnAttenteDeModeration());
            $oldEntity->setRole($entity->getRole());
            // Règle métier n°2 : Peut modifier le password de son propre compte
            if($entity->getIdCompte() == $currentUser->getIdCompte())
            {
                $oldEntity->setPassword($entity->getPassword());
            }
            return $this->getDao()->update($oldEntity);
            
        }
    }
    
}


?>