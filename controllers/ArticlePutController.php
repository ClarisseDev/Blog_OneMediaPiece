<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\controller\AbstractPutController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\services\ArticleService;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\model\Article;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\SessionManager;

class ArticlePutController extends AbstractPutController implements IController
{
    protected IService $service;
    private int $idArticle;
    private string $titre;
    private string $contenu;
    private string $estPublic;
    private bool $enAttenteDeModeration;
    private bool $estSupprime;
    private Compte $auteur;

    public function __construct(array $form)
    {
        // Initialisation des propriétés
        $this->idArticle = isset($form['id_article']) ? intval($form['id_article']) : 0;
        $this->titre = $form['titre'] ?? '';
        $this->contenu = $form['contenu'] ?? '';
        $this->estPublic = isset($form['estPublic']) ? Functions::stringToBool($form['estPublic']) : false;
        $this->enAttenteDeModeration = isset($form['enAttenteDeModeration']) ? Functions::stringToBool($form['enAttenteDeModeration']) : false;
        $this->estSupprime = isset($form['estSupprime']) ? Functions::stringToBool($form['estSupprime']) : false;

        $this->service = new ArticleService();
        parent::__construct($form, "ArticlePutController");
    }

    // Vérifier si les paramètres du formulaire sont présents et non nulls
    protected function checkForm()       
    {
        if (!isset($this->form['id_article']))
        {
            throw new HttpStatusException ("parameter id_article not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        if (!isset($this->form['titre']))     
        {
            throw new HttpStatusException ("parameter titre not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        if (!isset($this->form['contenu']))     
        {
            throw new HttpStatusException ("parameter contenu not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        if (!isset($this->form['estPublic']))     
        {
            throw new HttpStatusException ("parameter estPublic not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        if (!isset($this->form['enAttenteDeModeration']))     
        {
            throw new HttpStatusException ("parameter enAttenteDeModeration not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
        if (!isset($this->form['estSupprime']))     
        {
            throw new HttpStatusException ("parameter estSupprime not exists : "  . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 400);
        }
    }

    // Verifier la conformité des données du formulaire
    protected function checkCybersec()
    {
        if (!Functions::isNaturalInteger($this->form['id_article'])) {
            throw new HttpStatusException("parameter id_article is not a valid id", 400);
        }

        if (!Functions::isSanitizedString($this->form['titre'])) {
            throw new HttpStatusException("parameter titre is not a valid string", 400);
        }
        $this->titre = Functions::sanitizeString($this->form['titre']);

        // Utilisez isSanitizedContent pour le contenu
        if (!Functions::isSanitizedContent($this->form['contenu'])) {
            error_log("Contenu rejeté: " . $this->form['contenu']);
            throw new HttpStatusException("parameter contenu contains invalid characters", 400);
        }
        $this->contenu = $this->form['contenu']; 

        $this->estPublic = Functions::stringToBool($this->form['estPublic']);
        $this->enAttenteDeModeration = Functions::stringToBool($this->form['enAttenteDeModeration']);
        $this->estSupprime = Functions::stringToBool($this->form['estSupprime']);
    }

    protected function checkRights()
    {
        if (!SessionManager::isLogged()) {
            throw new HttpStatusException("Not Authenticated", 401);
        }

        $compteSession = SessionManager::getCompteFromSession();
        if (!$compteSession) {
            throw new HttpStatusException("Session Compte not found", 401);
        }

        // Récupérez l'article à modifier
        $article = $this->service->findById($this->idArticle);
        /**
         * @var Article $article
         */
        // Vérifiez que l'utilisateur connecté est bien l'auteur de l'article ou un admin/modérateur
        if ($compteSession->getIdCompte() != $article->getAuteur()->getIdCompte() && $compteSession->getRole()->getIdRole() < 2) {
            throw new HttpStatusException("You are not authorized to modify this article", 403);
        }
    }

    protected function createArray() : array {
        return [
            'id_article' => $this->idArticle,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'estPublic' => $this->estPublic,
            'enAttenteDeModeration' => $this->enAttenteDeModeration,
            'estSupprime' => $this->estSupprime
        ];
    }

    protected function createEntityFromArray(array $array) : IEntity {
        return Article::createFromForm($array);
    }

    protected function fetchOldEntity()
    {
        $this->oldEntity = $this->service->findById($this->idArticle);
    }

    protected function processRequest()
    {
        /** @var Article $oldEntity */
        $oldEntity = $this->oldEntity;

        // Mise à jour des propriétés de l'article
        $oldEntity->setTitre($this->titre);
        $oldEntity->setContenu($this->contenu);
        $oldEntity->setEstPublic($this->estPublic);
        $oldEntity->setEstEnAttenteDeModeration($this->enAttenteDeModeration);
        $oldEntity->setEstSupprime($this->estSupprime);

        $this->service->update($oldEntity);

        $this->response = [
            "status" => "success",
            "message" => "Article mis à jour avec succès",
            "id_article" => $this->idArticle
        ];
    }

    public function getService(): IService {
        return $this->service;
    }

}

?>