<?php

namespace OneMediaPiece_blog\utils\controller;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\exceptions\EntityNotFoundException;
use OneMediaPiece_blog\exceptions\SqlConstraintUniqueException;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\controller\AbstractController;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\service\AbstractService;
use OneMediaPiece_blog\utils\service\IService;

	/**
	 * Classe de base pour tous les controlleurs pour update. Par defaut, la 
	 * suppression 
	 * @author Cedric Chappert
	 *
	 */
	abstract class AbstractPutController extends AbstractController implements IController {
		
		protected IEntity $oldEntity;

		public function __construct(array $form, string $controllerName)
		{
			parent::__construct($form, $controllerName);
			$this->fetchOldEntity();
		}

		abstract protected function fetchOldEntity();
		abstract protected function createArray(): array;
		abstract protected function createEntityFromArray(array $array): IEntity;	
		
		function execute() : string {
			// $this->checkSiteType();
			$this->checkForm();		// Vérifier les données de formulaires
			$this->checkCybersec();		// Vérifier la cybersécurité
			$this->checkRights();		// Controller les droits d'accès
			try {
				$this->processRequest();	// traiter la requete
			} catch (EntityNotFoundException $e) {
				throw HttpStatusException::_404_Not_Found();
			}
			return $this->processResponse();	// fournir la réponse
		}
		
		/**
		 * Attention, si l'entité est séquentielle, ceci retourne l'enregistrement
		 * ayant la plus haute séquence.
		 * Il est nécessaire de redéfinir cette fonction pour appeler la méthode
		 * findByIdAndSequence définie dans le ISequenceService
		 */
		// protected function fetchOldEntity() {
		// 	$this->oldEntity = $this->getService()->findById($this->id);
		// }
		
		// protected abstract function checkForm();
		// {
		// 	Functions::checkFormElement($this->form, "id");
		// }
		
		/**
		 * Récupère la old Entity au passage
		 */
		// protected abstract function checkCybersec(); 
		// {
		// 	$this->id = Functions::getNaturalInteger($this->form, "id");
		// 	try {
		// 	    $this->fetchOldEntity();    // Récupère la vieille entité avant le controle des droits
		// 	    // TODO appeler le service ici pour vérifier si c'est updatable
		// 	    // 1 - rajouter une méthode dans l'interface IService isUpdatable($entity)
		// 	    // 2 - la méthode s'occupe de trouver si l'entité est updatable ou pas 
		// 	    // 3 - puis de lever la 403 ici -> supprimer le code de test du ConfigurationPutController 
		// 	} catch (EntityNotFoundException $e) {
		// 	    throw HttpStatusException::_404_Not_Found();
		// 	}
		// }
		
		/**
		 * If entity is HARD_DELETE, it's not possible to enter in this case
		 * because $this->checkCybersec fetch the old entity and this should
		 * raise a HttpStatusException::_404_Not_Found().<br>
		 * Else, the entity is in database and with type TRY_HARD_DELETE OR SOFT_DELETE.
		 * In case of TRY_HARD_DELETE, if a hard delete has been processed, then
		 * the behavior is the same as HARD_DELETE. Else the entity exists in
		 * database but has been tagged "as deleted" : by default, if entity has
		 * a deleted column with true value inside, a 403 Fobiden is raised 
		 */
		// protected abstract function checkRights(); 
		// {
		// 	// Si on est en try hard first le this de $this->oldEntity->isDeleted() va lever une exception si l'entité n'existe pas
		// 	// Donc le fetchOldEntity() de la fonction checkcybersec() devrai lever une erreur 404 en premier
		// 	// Construction dangereuse !
		// 	if ($this->oldEntity->getDeleteType() != IEntity::HARD_DELETE && $this->oldEntity->isDeleted()) {
		// 		throw HttpStatusException::_403_Forbidden();
		// 	}
		// }

		// protected abstract function processRequest(); 
		// {
		// 	$array = $this->createArray(); // Le tableau qui contient les informations pour créer l'entité
		// 	$newEntity = $this->createEntityFromArray($array); // crée l'entité depuis le tableau
		// 	$this->oldEntity = $this->doFind($this->id);
		// 	$this->doMerge($newEntity, $this->oldEntity);
		// 	try {
		// 		// TODO : ICI ne pas faire de doUpdate mais un updateTransaction($oldEntity) afin de pouvoir utiliser l'historique qui est déjà connecté a la méthode.

		// 		// On sauve bien la old entity dans laquelle on a mergé le new entity
		// 		$this->response = $this->doUpdate($this->oldEntity);
		// 	} catch ( SqlConstraintUniqueException $ex ) {
		// 		throw HttpStatusException::_498_Business_Error($ex);
		// 	}
		// }
		
		// protected function doFind(int $id) : IEntity {
		// 	return $this->getService()->findById($id);
		// }
		
		// protected function doUpdate(IEntity $entity) :int {
		// 	return $this->getService()->update( $entity );
		// }
		
		// protected function doMerge(IEntity $newEntity, IEntity $oldEntity): void {
		// 	$this->getService()->merge($newEntity, $this->oldEntity);
		// }
		
		// protected abstract function createArray() : array;
		
		// protected abstract function createEntityFromArray(array $array);
		
	}

?>