<?php
namespace OneMediaPiece_blog\controllers;

use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\services\ArticleService;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Article;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\SessionManager;
use DateTime;
use Exception;

class ArticlePostController extends AbstractController implements IController {
    private ArticleService $service;
    private string $titre;
    private string $contenu;

    public function __construct(array $form) {
        parent::__construct($form, "ArticlePostController");
        $this->service = new ArticleService();
    }

    protected function checkForm() {
        if (!isset($this->form['titre'])) {
            throw new HttpStatusException("parameter titre not exists", 400);
        }
        if (!isset($this->form['contenu'])) {
            throw new HttpStatusException("parameter contenu not exists", 400);
        }
    }

    protected function checkCybersec() {
        if (!Functions::isSanitizedString($this->form['titre'])) {
            throw new HttpStatusException("parameter titre is not a valid string", 400);
        }
        $this->titre = Functions::sanitizeString($this->form['titre']);

        if (!Functions::isSanitizedContent($this->form['contenu'])) {
            throw new HttpStatusException("parameter contenu contains invalid characters", 400);
        }
        $this->contenu = Functions::sanitizeString($this->form['contenu']);
    }

    protected function checkRights() {
        if (!SessionManager::isLogged()) {
            throw new HttpStatusException("Not Authenticated", 401);
        }
    }

    protected function processRequest()
    {
        $article = Article::createForCredential($this->titre, $this->contenu);
        $compteSession = SessionManager::getCompteFromSession();
        $article->setAuteur($compteSession);

        $article->setDateCreation(new DateTime());
        $article->setDateModification(new DateTime());
        $article->setEstPublic(false);
        $article->setEstEnAttenteDeModeration(true);
        $article->setEstSupprime(false);

        try {
            $idArticle = $this->service->insert($article);
            $this->response = [
                "status" => "success",
                "message" => "Article créé avec succès",
                "id_article" => $idArticle
            ];
        } catch (Exception $ex) {
            error_log("Erreur lors de la création de l'article: " . $ex->getMessage());
            throw new HttpStatusException("Erreur lors de la création de l'article", 500, $ex);
        }
    }

    protected function getService(): IService
    {
        return $this->service;
    }
}
?>