<?php
namespace OneMediaPiece_blog\daos;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Article;
use OneMediaPiece_blog\daos\CompteDao;
use OneMediaPiece_blog\model\Role;
use OneMediaPiece_blog\utils\dao\AbstractDao;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\BddSingleton;
use DateTime;
use PDO;
use PDOException;
use Exception;
use InvalidArgumentException;

class ArticleDao extends AbstractDao implements IDao
{

    public function __construct()
    {

    }

    function getTableName() : string
    {
        return "article";
    }

    function getPrimaryKeyName() : string
    {
        return "id_article";
    }

    function createEntityFromRow($row) : IEntity
    {
        $article = new Article();
        $article->setIdArticle( $row->id_article );
        $article->setTitre( $row->titre );
        $article->setContenu( $row->contenu );
        $article->setDateCreation( new DateTime($row->dateCreation) );
        $article->setDateModification( new DateTime($row->dateModification) );
        $article->setEstPublic( (bool)$row->estPublic );
        $article->setEstEnAttenteDeModeration( (bool)$row->estEnAttenteDeModeration );
        $article->setEstSupprime( (bool)$row->estSupprime );

        // Auteur
        $compteDao = new CompteDao();
        $auteur = $compteDao->findById( $row->fk_auteur );
        $article->setAuteur( $auteur );

        // Modérateur
        if ( ! is_null($row->fk_moderateur) )
        {
            $moderateur = $compteDao->findById( $row->fk_moderateur );
            $article->setModerateur( $moderateur );
        }

        if ( ! is_null($row->dateModeration) )
        {
            $article->setDateModeration( new DateTime($row->dateModeration) );
        }

        if ( ! is_null($row->motifModeration) )
        {
            $article->setMotifModeration( $row->motifModeration );
        }

        return $article;
    }

    // CREATE
    function insert(IEntity $entity) : int 
    { 
        /** @var Article $entity */
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "INSERT INTO " . $this->getTableName() . " (titre, contenu, dateCreation, dateModification, estPublic, enAttenteDeModeration, estSupprime, fk_auteur)
                VALUES (:titre, :contenu, :dCreation, :dModif, :estPublic, :enAttMod, :estSupp, :auteur)";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // Paramètres obligatoires
        // par défaut les dates de création et modification sont à la date courante
        // par défaut estPublic = false, enAttenteDeModeration = true, estSupprime = false
        $stmt->bindValue(":titre", $entity->getTitre(), PDO::PARAM_STR);
        $stmt->bindValue(":contenu", $entity->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(":dCreation", $entity->getDateCreation()->format(MYSQL_DATE_FORMAT), PDO::PARAM_STR);
        $stmt->bindValue(":dModif", $entity->getDateModification()->format(MYSQL_DATE_FORMAT), PDO::PARAM_STR);
        $stmt->bindValue(":estPublic", $entity->getEstPublic(), PDO::PARAM_BOOL);
        $stmt->bindValue(":enAttMod", $entity->getEstEnAttenteDeModeration(), PDO::PARAM_BOOL);
        $stmt->bindValue(":estSupp", $entity->getEstSupprime(), PDO::PARAM_BOOL);
        $stmt->bindValue(":auteur", $entity->getAuteur()->getIdCompte(), PDO::PARAM_INT);
        
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute insert query for Article : " . $entity->getTitre() . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Récupérer l'id auto-incrémenté généré par la BDD
            $id = intval($pdo->lastInsertId());
            return $id;
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":titre" => $entity->getTitre(),
                ":contenu" => $entity->getContenu(),
                ":dCreation" => $entity->getDateCreation()->format(MYSQL_DATE_FORMAT),
                ":dModif" => $entity->getDateModification()->format(MYSQL_DATE_FORMAT),
                ":estPublic" => $entity->getEstPublic(),
                ":enAttMod" => $entity->getEstEnAttenteDeModeration(),
                ":estSupp" => $entity->getEstSupprime(),
                ":auteur" => $entity->getAuteur()->getIdCompte()
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database insert failed for Article: " . $entity->getTitre(), 500, $ex);
        }
    }

    // RETREIVE
    function isValidCredential($article) : ?int
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
        /** @var Article $article */
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "SELECT id_article FROM " . $this->getTableName() . " WHERE login = :login AND estSupprime = false";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // Paramètres obligatoires
        $stmt->bindValue(":login", $article->getLogin(), PDO::PARAM_STR);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute select query for login : " . $article->getLogin() . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Récupérer le tuple
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if($row === false)
            {
                // Pas de compte avec ce login
                return null;
            }
            // Vérifier le mot de passe
            if( password_verify( $article->getPassword(), $row->password) )
            {
                // Mot de passe OK, retourner l'id du compte
                return intval($row->id_article);
            }
            else
            {
                // Mauvais mot de passe
                return null;
            }
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":login" => $article->getLogin()
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select failed for login: " . $article->getLogin(), 500, $ex);
        }
    }

    function findById(int $id): IEntity
    {
        // si l'utilisateur est l'auteur ou un admin ou un modérateur, il peut voir l'article même s'il n'est pas public
        // connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // requête SQL
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryKeyName() . " = :id AND estSupprime = false";
        // préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // paramètres obligatoires
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute select query for Article id : " . $id . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Récupérer le tuple
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if($row === false)
            {
                // Pas d'article avec cet id
                throw new HttpStatusException("Article not found: " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__, 404);
            }
            return $this->createEntityFromRow($row);
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":id" => $id
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select failed for Article id: " . $id, 500, $ex);
        }
    }

    function findByIdPublic (int $id): ?IEntity
    {
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryKeyName() . " = :id AND estPublic = true AND estSupprime = false";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // Paramètres obligatoires
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute select query for Article id : " . $id . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Récupérer le tuple
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if($row === false)
            {
                // Pas d'article avec cet id
                return null;
            }
            return $this->createEntityFromRow($row);
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":id" => $id
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select failed for Article id: " . $id, 500, $ex);
        }
    }

    function findAll()
    {
        // si l'utilisateur est l'auteur ou un admin ou un modérateur, il peut voir tous les articles même s'ils ne sont pas publics
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE estSupprime = false ORDER BY dateCreation DESC";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute select all query for Articles " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            $articles = [];
            // Récupérer les tuples
            while($row = $stmt->fetch(PDO::FETCH_OBJ))
            {
                $articles[] = $this->createEntityFromRow($row);
            }
            return $articles;
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql);

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select all failed for Articles", 500, $ex);
        }
    }

    function findAllPublic(): array
    {
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE estPublic = true AND estSupprime = false ORDER BY dateCreation DESC";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        try {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute()) {
                throw new HttpStatusException("Failed to execute select all public query for Articles " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            $articles = [];
            // Récupérer les tuples
            while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $articles[] = $this->createEntityFromRow($row);
            }
            return $articles;
        } catch (PDOException $ex) {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql);
            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select all public failed for Articles", 500, $ex);
        }
    }

    function findAllByAuthor(int $authorId) : array
    {
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE fk_auteur = :authorId AND estSupprime = false ORDER BY dateCreation DESC";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // Paramètres obligatoires
        $stmt->bindValue(":authorId", $authorId, PDO::PARAM_INT);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute select all by author query for Articles " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            $articles = [];
            // Récupérer les tuples
            while($row = $stmt->fetch(PDO::FETCH_OBJ))
            {
                $articles[] = $this->createEntityFromRow($row);
            }
            return $articles;
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":authorId" => $authorId
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database select all by author failed for Articles", 500, $ex);
        }
    }

    // UPDATE
    function update(IEntity $entity): IEntity
    {
        if (!($entity instanceof Article)) {
            throw new InvalidArgumentException("Expected instance of Article");
        }

        $pdo = BddSingleton::getInstance()->getPdo();
        $sql = "UPDATE " . $this->getTableName() . "
                SET titre = :titre,
                    contenu = :contenu,
                    dateModification = :dModif,
                    estPublic = :estPublic,
                    enAttenteDeModeration = :enAttMod,
                    estSupprime = :estSupp
                WHERE id_article = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":titre", $entity->getTitre(), PDO::PARAM_STR);
        $stmt->bindValue(":contenu", $entity->getContenu(), PDO::PARAM_STR);
        $stmt->bindValue(":dModif", (new DateTime())->format(MYSQL_DATE_FORMAT), PDO::PARAM_STR);
        $stmt->bindValue(":estPublic", $entity->getEstPublic(), PDO::PARAM_BOOL);
        $stmt->bindValue(":enAttMod", $entity->getEstEnAttenteDeModeration(), PDO::PARAM_BOOL);
        $stmt->bindValue(":estSupp", $entity->getEstSupprime(), PDO::PARAM_BOOL);
        $stmt->bindValue(":id", $entity->getIdArticle(), PDO::PARAM_INT);

        try {
            if (!$stmt->execute()) {
                throw new HttpStatusException("Failed to execute update query for Article", 500);
            }
            return $entity;
        } catch (PDOException $ex) {
            error_log("SQL Error: " . $ex->getMessage());
            throw new HttpStatusException("Database update failed for Article", 500, $ex);
        }
    }


    // DELETE
    function delete(int $id) : bool
    {
        // Vérifier si l'entité à supprimer existe
        $existingEntity = $this->findById($id);
        if (is_null($existingEntity)) 
        {
            throw new HttpStatusException("Article with ID $id not found for deletion. " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 404);
        }
        // Connexion à la BDD
        $pdo = BddSingleton::getInstance()->getPdo();
        // Requête SQL
        $sql = "DELETE FROM " . $this->getTableName() . " WHERE id_article = :id";
        // Préparer la requête SQL
        $stmt = $pdo->prepare($sql);
        // Paramètre obligatoire
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        try
        {
            // Exécuter et vérifier l'exécution de la requête
            if(!$stmt->execute())
            {
                throw new HttpStatusException("Failed to execute delete query for Article ID : " . $id . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 500);
            }
            // Vérifier si une ligne a été affectée (supprimée)
            if ($stmt->rowCount() === 0) 
            {
                throw new HttpStatusException("No Article deleted, ID may not exist: " . $id . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 404);
            }
            return true;
        } 
        catch (PDOException $ex) 
        {
            // Log détaillé pour le débogage
            error_log("SQL Error: " . $ex->getMessage() . " | Query: " . $sql . " | Parameters: " . json_encode([
                ":id" => $id
            ]));

            // Lever une exception HTTP avec un message clair
            throw new HttpStatusException("Database delete failed for Article ID: " . $id, 500, $ex);
        }
    }
}

?>