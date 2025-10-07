<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\controller\AbstractGetController;
use OneMediaPiece_blog\utils\service\IService; 
use OneMediaPiece_blog\services\ArticleService;
use OneMediaPiece_blog\utils\SessionManager;

class ArticleGetController extends AbstractGetController implements IController
{
    protected IService $service; 
    protected $currentSessionOwner; 

    public function __construct(array $form)
    {
        parent::__construct($form);
        $this->service = new ArticleService(); 
        $sessionInfo = SessionManager::getCompteIdFromSession();
        $this->currentSessionOwner = $this->compteService->findById($sessionInfo);
    }

    protected function getService() : IService
    {
        return $this->service;
    }
    protected function processRequest()
    {
        $this->response = $this->service->findById($this->id);
    }

}

?>