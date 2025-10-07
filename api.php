<?php 

namespace OneMediaPiece_blog;

define('ROOT', __DIR__);
require_once ROOT . '/utils/Server.php';
\OneMediaPiece_blog\utils\Server::bootstrap();

// Gestion de la session
use OneMediaPiece_blog\utils\SessionManager;
SessionManager::manageSession();

// MAINTENANT seulement on peut utiliser les autres classes
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\exceptions\SqlAccessDeniedException;
use OneMediaPiece_blog\exceptions\SqlConnectionRefusedException;
use OneMediaPiece_blog\utils\Router;
use Throwable;
use OneMediaPiece_blog\utils\logs\Logger;

define('START_TIME', 'start_time');
define('COMPTE_ID', 'compte_id');
		
	try {
		$controller = (new Router())->createController();
		Logger::printInErrorLog("Controller successfully created : " . $controller->getName());
		header('Content-Type: application/json'); // ← AVANT !
		$response = $controller->execute();
		echo $response;
	} catch (HttpStatusException $e) {
		$e->raiseHttpStatus();
	} catch (SqlConnectionRefusedException $e) {
		HttpStatusException::_500_Internal_Server_Error($e, "SQL Server seems shut down")->raiseHttpStatus();
	} catch (SqlAccessDeniedException $e) {
		HttpStatusException::_500_Internal_Server_Error($e, "Database access seems to be bad configured")->raiseHttpStatus();
	} catch (Throwable $e) {
		HttpStatusException::_500_Internal_Server_Error($e, "An unmanaged exception occurs")->raiseHttpStatus();
	}
?>