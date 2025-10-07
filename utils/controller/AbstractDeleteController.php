<?php

namespace OneMediaPiece_blog\utils\controller;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\exceptions\SqlConstraintForeignKeyException;
use OneMediaPiece_blog\utils\service\AbstractService;
use OneMediaPiece_blog\utils\service\IService;

	/** Classe de base pour tous les controlleurs */
	abstract class AbstractDeleteController extends AbstractController implements IController {

		protected int $id;
		protected IService $service;
		
		// Constructeur, permet de faire new ClassName(...)
		public function __construct($form, $controllerName) {
			parent::__construct($form, $controllerName);
			$this->service = $this->getService();
		}

		function getService() : IService {
			return $this->service;
		}

		function checkForm() {
			Functions::checkFormElement($this->form, "id");
		}
		
		function checkCybersec() {
			$this->id = Functions::getNaturalInteger($this->form, "id");
		}

		public function processRequest() {
			try {
				$this->response = $this->getService()->delete($this->id);
			} catch (SqlConstraintForeignKeyException $ex) {
				throw HttpStatusException::_498_Business_Error($ex);
			}
		}
		
	}

?>