<?php

namespace OneMediaPiece_blog\daos;
use DateTime;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\daos\RoleDao;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\BddSingleton;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\dao\AbstractDao;
use OneMediaPiece_blog\model\Role;
use PDO;
use PDOException;
use InvalidArgumentException;

class CompteDao extends AbstractDao implements IDao
{
    private RoleDao $roleDao;

    public function __construct()
    {
        $this->roleDao= new RoleDao();
    }

    function getTableName() : string
    {
        return "compte";
    }

    function getPrimaryKeyName() : string
    {
        return "id_compte";
    }

    function createEntityFromRow($row): IEntity
    {
        $compte = Compte::createFromRow($row, true);

        $role = new Role();
        $role->setIdRole(intval($row->fk_role ?? 0)); 
        $compte->setRole($role);

        return $compte;
    }

    // CREATE
    function insert(IEntity $entity) : int 
    { 
        /** @var Compte $entity */
        $pdo = BddSingleton::getInstance()->getPdo(); 
        $sql = "INSERT INTO compte (" 
            . "login, password, pseudo, dateCreation, dateModification, estSupprime, estSignale, estBanni, enAttenteDeModeration, fk_role) " 
            . " VALUES (:log, :pwd, :pseudo, :dCreation, :dModif, :estSupp, :estSign, :estBan, :enAttMod, :idRole)"; 
        $stmt = $pdo->prepare($sql); 
        $stmt->bindValue(":log", $entity->getLogin() ); 
        $stmt->bindValue(":pwd", password_hash( $entity->getPassword(), PASSWORD_BCRYPT) ); 
        $stmt->bindValue(":pseudo", $entity->getPseudo() ); 
        $stmt->bindValue(":dCreation", $entity->getDateCreation()->format(MYSQL_DATE_FORMAT) ); 
        $stmt->bindValue(":dModif", $entity->getDateModification()->format(MYSQL_DATE_FORMAT) ); 
        $stmt->bindValue(":estSupp", $entity->getEstSupprime(), PDO::PARAM_BOOL); 
        $stmt->bindValue(":estSign", $entity->getEstSignale(), PDO::PARAM_BOOL); 
        $stmt->bindValue(":estBan", $entity->getEstBanni(), PDO::PARAM_BOOL); 
        $stmt->bindValue(":enAttMod", $entity->getEnAttenteDeModeration(), PDO::PARAM_BOOL); 
        $stmt->bindValue(":idRole", $entity->getRole()->getIdRole(), PDO::PARAM_INT ); 
        try 
        { 
            $stmt->execute(); 
            return $pdo->lastInsertId(); 
        } catch (PDOException $ex) 
        { 
            // 2 errerus potentielles
            // SQLSTATE[23000]: Integrity constraint violation: 
            // 1062 Duplicate entry 'user7@gmail.com' for key 'login_UNIQUE'
            error_log($ex->getMessage());
            if (str_starts_with($ex->getMessage(), "SQLSTATE[23000]:"))
            {
                $msg = explode(": ", $ex->getMessage())[2];
                if (str_starts_with($msg, "1062 "))
                {
                    $msg = explode(" ", $msg)[6];
                    // throw new HttpStatusException("UNIQUE : " . $msg, 499); 
                    throw new HttpStatusException($msg . " - already exists ", 499); 
                }
            }
            throw new HttpStatusException($ex->getMessage(), 500, $ex); 
        } 
    }

    // RETREIVE
    function isValidCredential($compte) : ?int
    {
        //Dans un 1er temps je vais chercher tous les tuples qui ont pour login $compte-getLogin();
        $pdo = BddSingleton::getInstance()->getPdo();
        // TODO : Attention = comptes supprimés, banni, en modération (if ===supprimés || banni || modération return false)
        $stmt = $pdo->prepare("SELECT id_compte, password FROM compte WHERE login = ? LIMIT 1"); // LIMIT 1 ne retourner max qu'une seule ligne et permet d'arrêter la recherche dès qu'une réponse est trouvée
        $stmt->bindValue(1, $compte->getLogin());
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $row = $stmt->fetch();
        if ( ! $row )
        {
            return NULL;
        }
        // Tester si le password et le hashage correspondent
        if (password_verify($compte->getPassword(), $row->password))
        {
            return $row->id_compte;
        }
        return NULL;
    }

    public function findById(int $id): Compte
    {
        $pdo = BddSingleton::getInstance()->getPdo();
        $sql = "SELECT c.*, r.id_role as fk_role FROM compte c JOIN role r ON c.fk_role = r.id_role WHERE c.id_compte = :idCompte";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":idCompte", $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            throw new HttpStatusException("Entity not found for ID : " . $id . " " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__, 404);
        }

        return $this->createEntityFromRow($row);
    }

    // UPDATE
    function update(IEntity $entity): IEntity
    {
        // Vérifier que le paramètre $entity est un objet type Compte
        if (!($entity instanceof Compte))
        {
            throw new InvalidArgumentException("Expected instance of Compte");
        }

        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo(); 
        // Requête SQL
        $sql = "UPDATE compte SET
                    password = :password,
                    dateModification = :dModif,
                    estSupprime = :estSupp,
                    estSignale = :estSign,
                    estBanni = :estBan, 
                    enAttenteDeModeration = :enAttMod,
                    fk_role = :role
                WHERE id_compte = :idCompte";
    

        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
    
        // Récupérer l'id du compte à mettre à jour
        $stmt->bindValue(":idCompte", $entity->getIdCompte(), PDO::PARAM_INT);
        // Mettre à jour la date de modification
        $stmt->bindValue(":dModif", (new DateTime())->format(MYSQL_DATE_FORMAT));
    
        // Paramètres conditionnels
        $hashedPassword = password_hash( $entity->getPassword(), PASSWORD_BCRYPT);
        $stmt->bindValue(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(":enAttMod", $entity->getEnAttenteDeModeration(), PDO::PARAM_BOOL);
        $stmt->bindValue(":estSupp", $entity->getEstSupprime(), PDO::PARAM_BOOL);
        $stmt->bindValue(":estSign", $entity->getEstSignale(), PDO::PARAM_BOOL);
        $stmt->bindValue(":estBan", $entity->getEstBanni(), PDO::PARAM_BOOL);
        $stmt->bindValue(":role", $entity->getRole()->getIdRole(), PDO::PARAM_INT);
    
        try 
        {
            // Exécuter et vérifier l'exécution de la mise à jour des paramètres de l'objet
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute update query for ID : " . $entity->getIdCompte() . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Récupérer l'objet mis à jour depuis la base de données
            $entity = $this->findById($entity->getIdCompte());
            return $entity;
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":idCompte" => $entity->getIdCompte(),
                ":password" => $hashedPassword,
                ":dModif" => (new DateTime())->format(MYSQL_DATE_FORMAT),
                ":enAttMod" => $entity->getEnAttenteDeModeration(),
                ":estSupp" => $entity->getEstSupprime(),
                ":estSign" => $entity->getEstSignale(),
                ":estBan" => $entity->getEstBanni(),
                ":role" => $entity->getRole()->getIdRole(),
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database update failed for ID: " . $entity->getIdCompte(), 500, $ex);
        }
    }
}

?>