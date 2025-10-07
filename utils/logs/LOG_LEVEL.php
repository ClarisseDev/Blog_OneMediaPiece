<?php

namespace OneMediaPiece_blog\utils\logs;

use Exception;

enum LOG_LEVEL: int {
	
	case OFF   = -2; // Désactive tous les messages de log (le plus haut niveau).
	case ALL   = -1; // Active tous les messages de log (le plus bas niveau).
	case FATAL =  0; // Erreurs graves qui provoquent l'arrêt complet de l'application.
	case ERROR =  1; // Erreurs qui empêchent le fonctionnement d'une partie du système.
	case WARN  =  2; // Avertissements sur des situations inhabituelles, mais non critiques.
	case INFO  =  3; // Informations générales sur l'exécution normale de l'application.
	case DEBUG =  4; // Informations détaillées utiles pour le débogage.
	case TRACE =  5; // Messages très détaillés pour le débogage approfondi.
	
 
	public static function fromString(string $value) : LOG_LEVEL {
		switch ($value) {
			case "OFF"   : return LOG_LEVEL::OFF;
			case "FATAL" : return LOG_LEVEL::FATAL;
			case "ERROR" : return LOG_LEVEL::ERROR;
			case "WARN"  : return LOG_LEVEL::WARN;
			case "INFO"  : return LOG_LEVEL::INFO;
			case "DEBUG" : return LOG_LEVEL::DEBUG;
			case "TRACE" : return LOG_LEVEL::TRACE;
			case "ALL"   : return LOG_LEVEL::ALL;
			default : throw new Exception("Log Level can't be parsed " . $value);
		}
	}
	
}
?>