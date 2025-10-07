<?php
namespace OneMediaPiece_blog\services;

use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\daos\ArticleDao;
use OneMediaPiece_blog\model\Article;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\service\AbstractService;
use InvalidArgumentException;

class ArticleService extends AbstractService implements IService
{
    protected ArticleDao $dao;
    protected CompteService $compteService;

    public function __construct()
    {
        $this->dao = new ArticleDao();
        $this->compteService = new CompteService();
    }

    function getDao() : IDao { 
        return $this->dao;
    }

    // CREATE
    function isValidCredential(Article $article) : ?int
    {
        return $this->dao->isValidCredential($article);
    }


    function insert(IEntity $entity): int
    {
        /** @var Article $entity */
        if(!($entity instanceof Article))
        {
            throw new InvalidArgumentException("Expected instance of Article "  . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__);
        }

        // Vérifiez que l'utilisateur est connecté
        if(!SessionManager::isLogged()) {
            throw new HttpStatusException("Unlogged -> Unauthorized to insert : " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 401);
        }

        // On crée un objet qui représente la personne connectée
        $compteSession = SessionManager::getCompteFromSession();
        if (!$compteSession) {
            throw new HttpStatusException("Session Compte not found", 401);
        }

        // REDACTEUR
        if ($compteSession->getRole()->getIdRole() == 1) {
            // Règle métier : Peut créer un article
            return $this->getDao()->insert($entity);
        }

        // MODO
        if ($compteSession->getRole()->getIdRole() == 2) {
            // Règle métier : Peut créer un article
            return $this->getDao()->insert($entity);
        }

        // ADMIN
        if ($compteSession->getRole()->getIdRole() == 3) {
            // Règle métier : Peut créer un article
            return $this->getDao()->insert($entity);
        }

        throw new HttpStatusException("Forbidden to insert : " . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 403);
    }


    // RETREIVE
    function findById(int $id): IEntity {
        // Récupérer le rôle et l'ID du compte depuis la session
        $roleSession = SessionManager::getRoleIdFromSession();
        $compteSession = SessionManager::getCompteIdFromSession(); 

        // Vérifier que l'article existe
        $article = $this->getDao()->findById($id);
        if (!$article instanceof Article) {
            throw new HttpStatusException("Article introuvable", 404);
        }

        // Vérifier si l'article est supprimé ou en attente de modération
        if ($article->getEstSupprime() || $article->getEstEnAttenteDeModeration()) {
            if (!$roleSession || $roleSession < 2) {
                throw new HttpStatusException("Article introuvable", 404);
            }
            return $article;
        }

        // Vérifier les droits d'accès
        if ($article->getAuteur()->getIdCompte() == $compteSession || $roleSession >= 2 || $article->getEstPublic()) {
            return $article;
        } else {
            throw new HttpStatusException("Accès refusé à cet article", 403);
        }
    }

    function findAll(): array
    {
        $roleSession = SessionManager::getRoleIdFromSession();
        $compteSession = SessionManager::getCompteIdFromSession();

        if (!$compteSession) {
            return $this->dao->findAllPublic();
        }

        // Si l'utilisateur est un admin ou un modo, il peut voir tous les articles
        if ($roleSession >= 2) {
            return $this->getDao()->findAll();
        } else {
            // Sinon, il ne voit que ses propres articles ou les articles publics
            $allArticles = $this->getDao()->findAll();
            $filteredArticles = [];
            foreach ($allArticles as $article) {
                if ($article instanceof Article) {
                    if ($article->getEstPublic() || $article->getAuteur()->getIdCompte() == $compteSession) {
                        $filteredArticles[] = $article;
                    }
                }
            }
            return $filteredArticles;
        }
    }

    // UPDATE
    function update(IEntity $entity): IEntity
    {
        if (!($entity instanceof Article)) {
            throw new InvalidArgumentException("Expected instance of Article");
        }

        if (!SessionManager::isLogged()) {
            throw new HttpStatusException("Unlogged -> Unauthorized to update", 401);
        }

        $compteSession = SessionManager::getCompteFromSession();
        if (!$compteSession) {
            throw new HttpStatusException("Session Compte not found", 401);
        }

        // Récupérez l'article existant
        $oldEntity = $this->getDao()->findById($entity->getIdArticle());

        /**
         * @var Article $oldEntity
         */
        // Vérifiez les droits de l'utilisateur
        if ($compteSession->getRole()->getIdRole() == 1 && $oldEntity->getAuteur()->getIdCompte() != $compteSession->getIdCompte()) {
            throw new HttpStatusException("Forbidden to update this article", 403);
        }

        // Mise à jour des propriétés de l'article
        $oldEntity->setTitre($entity->getTitre());
        $oldEntity->setContenu($entity->getContenu());

        if ($compteSession->getRole()->getIdRole() >= 2) {
            $oldEntity->setEstPublic($entity->getEstPublic());
            $oldEntity->setEstEnAttenteDeModeration($entity->getEstEnAttenteDeModeration());
        }

        if ($compteSession->getRole()->getIdRole() == 3) {
            $oldEntity->setEstSupprime($entity->getEstSupprime());
        }

        return $this->getDao()->update($oldEntity);
    }

    
}
