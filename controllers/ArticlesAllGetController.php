<?php

namespace OneMediaPiece_blog\controllers;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\service\IService; 
use OneMediaPiece_blog\services\ArticleService;
use OneMediaPiece_blog\exceptions\HttpStatusException;

class ArticlesAllGetController extends AbstractController implements IController
{
    protected IService $service; 
    protected string $controllerName = "ArticlesAllGetController";

    public function __construct(array $form, string $controllerName)
    {
        parent::__construct($form, $controllerName);
        $this->service = new ArticleService();
    }

    protected function checkForm()
    {
        // No parameters to check for this controller
    }

    protected function checkCybersec()
    {
        // No parameters to validate for this controller
    }

    protected function getService() : IService
    {
        return $this->service;
    }

    protected function processRequest()
    {
        try {
            $articles = $this->service->findAll();
            $this->response = $articles ?? [];
        } catch (HttpStatusException $e) {
            http_response_code($e->getCode());
            $this->response = ['error' => $e->getMessage()];
        } catch (\Throwable $t) {
            http_response_code(500);
            $this->response = ['error' => $t->getMessage()];
        }
    }
}


?>