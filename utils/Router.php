<?php

namespace OneMediaPiece_blog\utils;

use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\controller\IController;
use OneMediaPiece_blog\utils\logs\LOG_LEVEL;
use OneMediaPiece_blog\utils\logs\Logger;
use OneMediaPiece_blog\utils\Functions;
use Error;
use Throwable;

class Router {
	
	private array $form;
	private string $route;
	
	public function __construct() {
	    $this->form = self::extractForm();
	    $this->route = self::extractRoute();
	}
	
	private static function extractForm() : array {
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET' : return $_GET;
			case 'POST' : return $_POST;
			case 'PUT' : // Non géré en php, il va falloir extraire depuis le formulaire brut
				$raw = file_get_contents('php://input'); 	// Le formulaire brut
				$form = [];					// mon futur tableau associatif
				parse_str($raw, $form);	// Function interne de transfert du raw vers mon $form
				return $form;
			case 'DELETE' : return $_GET;
			default : throw HttpStatusException::_405_Method_Not_Allowed($_SERVER['REQUEST_METHOD']);
		}
	}
	
	/**
	 * Cette fonction va extraire le paramètre "route" du formulaire
	 * puis le retourner, SI il n'est pas présent, le serveur retournera
	 * une erreur 400 Bad Request car on considère que l'on ne comprend
	 * pas la demande du client.
	 */
	private function extractRoute() : string {
		if ( ! isset( $this->form['route'] ) ) {
			// Pas de Fall back
			// return "Accueil";
			throw HttpStatusException::_400_Bad_Request("Missing parameter route", "CYBERSEC Receive bad request, no param route", LOG_LEVEL::WARN);
		}
		// On veut sécuriser la syntaxe de la route
		$ROUTE = $this->form['route'];
		if ( Functions::isWord($ROUTE) ) {
			return $ROUTE;
		}
		throw HttpStatusException::_400_Bad_Request("Route Wrong Format", "CYBERSEC Receive bad request, param is not a route", LOG_LEVEL::WARN);
	}
	
	function createController() : IController {
		// Start Debug
// 		try {
// 			$controller = new \OneMediaPiece_blog\controllers\TachePostController($this->form, "TachePostController");
// 			return $controller;
// 		} catch (Throwable $e) { // Parser et cie...
// 			Logger::printInErrorLog($e, LOG_LEVEL::FATAL);
// 			Logger::logInErrorLog("Error in dynamic controller load", LOG_LEVEL::FATAL);
// 			die();
// 		}
		// End Debug
		
		// Je récupère la méthode, par exemple GET, je veux Get
		$METHOD = strtolower( $_SERVER['REQUEST_METHOD'] ); // Tout en minuscule
		$METHOD = ucfirst($METHOD); // Première lettre en Majuscule
		// Je construis le nom de mon controlleur, je vais le réutiliser ailleurs
		$CONTROLLER_NAME = $this->route . $METHOD . "Controller";
		$FILE = ROOT . "/controllers/" . $CONTROLLER_NAME . ".php";
		if ( ! file_exists($FILE) ) {
			throw HttpStatusException::_404_Not_Found(null, "Unknown Controller " . $this->route . $METHOD, LOG_LEVEL::WARN);
		}
		try {
			$CONTROLLER_NAME = "\\OneMediaPiece_blog\\controllers\\" . $CONTROLLER_NAME;
			$controller = new $CONTROLLER_NAME($this->form, $CONTROLLER_NAME);
			return $controller;
		} catch (Throwable $e) { // Parser et cie...
			Logger::printInErrorLog($e, LOG_LEVEL::FATAL);
			Logger::logInErrorLog("Error in dynamic controller load", LOG_LEVEL::FATAL);
			die();
		}
	}
}
?>