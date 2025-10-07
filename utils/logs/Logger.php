<?php

namespace OneMediaPiece_blog\utils\logs;

use OneMediaPiece_blog\utils\Server;


class Logger {
	
	public static function printInErrorLog($obj, LOG_LEVEL $level = LOG_LEVEL::DEBUG) : void {
		self::logInErrorLog(print_r($obj,true), $level);
	}
	
	public static function logInErrorLog(string $message, LOG_LEVEL $level = LOG_LEVEL::DEBUG) : void {
		if (Server::getCURRENT_LOG_LEVEL() == LOG_LEVEL::OFF) {
			return;
		}
		if (Server::getCURRENT_LOG_LEVEL() == LOG_LEVEL::ALL) {
			error_log($level->name . " : " . $message);
			return;
		}
		if ($level->value <= Server::getCURRENT_LOG_LEVEL()->value) {
			error_log($level->name . " : " . $message);
			return;
		}
		// error_log("WTF ????" . $level->name);
	}
	
	public static function warn_log(string $msg) {
		trigger_error($msg, E_USER_WARNING);
	}
	
	public static function varDumpInErrorLog($data) {
		ob_start();
		var_dump($data);
		$output = ob_get_clean();
		self::logInErrorLog($output);
	}
	
}
?>
