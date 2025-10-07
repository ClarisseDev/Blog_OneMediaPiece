<?php

namespace OneMediaPiece_blog\utils;

use OneMediaPiece_blog\utils\logs\LOG_LEVEL;
use OneMediaPiece_blog\utils\logs\Logger;
// use OneMediaPiece_blog\services\ConfigurationService;
use OneMediaPiece_blog\exceptions\EntityNotFoundException;
use Exception;

class Server {
	
	private static LOG_LEVEL $CURRENT_LOG_LEVEL = LOG_LEVEL::ALL;

	public static function bootstrap() {
    	self::setupInitialEnv();
		self::setupInitialLogLevel();
		self::initializeConstants();
		self::forcePhpIni();
		self::registerClassAutoloading();
		//self::forceServerParam();
		SessionManager::getInstance()->manageSession();
	}
	
	public static function setupInitialEnv() : void {
		if ( ! defined("ENV") ) { // Just in case to avoid some accidents
			error_log("No environment defined, force to 'dev'.");
			define("ENV", "dev");
		}
	}
	
	public static function setupInitialLogLevel() : void {
		include "utils/logs/LOG_LEVEL.php"; // No choice...
		if ( ENV == "test") {
			self::$CURRENT_LOG_LEVEL = LOG_LEVEL::ALL;
		} else if (ENV == "prod") {
			self::$CURRENT_LOG_LEVEL = LOG_LEVEL::INFO;
		} else { // dev
			self::$CURRENT_LOG_LEVEL = LOG_LEVEL::DEBUG;
		}
		error_log("Initial LOG_LEVEL for Bootstrap " . self::$CURRENT_LOG_LEVEL->name . " for environment " . ENV);
	}
	
	private static function forcePhpIni() {
		error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_STRICT & ~E_NOTICE & ~E_PARSE);
		ini_set('display_errors', 'off');
	}
	
	private static function registerClassAutoloading() {
		// Chargement dynamique des classes uniquement environnement NON test
		// Seulement si ENV n'est pas test, dans le cas ou ENV n'est pas défini
		// c'est l'environnement de dev qui est chargé par défaut
		// L'environnement de test dispose de son propre auto loader
		if (ENV != "test") {
			spl_autoload_register(function ($name) {
				$fileName = str_replace('\\', '/',  $name ) . ".php";
			if (str_starts_with($fileName, "OneMediaPiece_blog/")) {
				$fileName = str_replace("OneMediaPiece_blog/", "", $fileName);
			}
				require_once(ROOT . "/" . $fileName);
				Logger::logInErrorLog(">> Autoload : " . $name, LOG_LEVEL::TRACE);
			});
		}
	}
	
	public static function initializeConstants() {
		define("MYSQL_DATE_FORMAT", "Y-m-d H:i:s");
	}
	
	// public static function forceServerParam() {
	// 	$confSvc = new ConfigurationService();
	// 	try {
	// 		// On force la time zone telle qu'elle est définie dans la base de données
	// 		$key = "php.date.timezone";
	// 		$timeZone = $confSvc->findByKey($key);
	// 		date_default_timezone_set($timeZone->getValue());
	// 		$key = "LOG_LEVEL";
	// 		$logLevel = $confSvc->findByKey("LOG_LEVEL");
	// 		self::$CURRENT_LOG_LEVEL = LOG_LEVEL::fromString($logLevel->getValue());
	// 	} catch(EntityNotFoundException $ex) {
	// 		throw new Exception("Missing configuration : " . $key);
	// 	}
	// }
	
	public static function getCURRENT_LOG_LEVEL() : LOG_LEVEL {
		return self::$CURRENT_LOG_LEVEL;
	}
	
}
?>