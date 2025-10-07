<?php

namespace OneMediaPiece_blog\exceptions;

use Exception;
use Throwable;
use OneMediaPiece_blog\utils\logs\LOG_LEVEL;
use OneMediaPiece_blog\utils\Functions;
use OneMediaPiece_blog\utils\logs\Logger;

class HttpStatusException extends Exception {

	private mixed $debugMessage;

	private mixed $bodyMessage;

	private LOG_LEVEL $logLevel;

	/**
     * @return string
     */
    public function getBodyMessage()
    {
        return $this->bodyMessage;
    }

	/**
	 * {@inheritDoc}
	 * @see Exception::__construct()
	 */
	public function __construct(string $message, int $code, Throwable $previous = null, mixed $bodyMessage = null, mixed $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::INFO) {
		parent::__construct($message, $code, $previous);
		$this->bodyMessage = $bodyMessage;
		$this->debugMessage = $debugMessage;
		$this->logLevel = $logLevel;
	}

	/**
	 * @return string
	 */
	public function getDebugMessage() : ?string {
		return $this->debugMessage;
	}

	public function raiseHttpStatus() : void {
		if (isset($this->debugMessage)) {
			Logger::logInErrorLog("Debug Message : " . $this->debugMessage, $this->logLevel);
		}
		if ( ! is_null($this->getPrevious()) ) { // L'exception HTTP ne nous interesse pas directement, on a une erreur plus grave
			Logger::logInErrorLog($this->getPrevious()->getMessage() . "\n" . $this->getPrevious()->getTraceAsString(), $this->logLevel);
		}
		self::headerAndDie("HTTP/1.1 " . $this->code . " " . $this->message,  $this->bodyMessage);
	}

	public static function headerAndDie(string $header, mixed $bodyMessage = null) {
		header($header);
		if ( isset($bodyMessage) ) {
			header('Content-Type: application/json');
			echo json_encode($bodyMessage);
		}
		die();
	}

	public static function headerCustom(int $code, string $label, mixed $bodyMessage = null) {
	    self::headerAndDie("HTTP/1.1 " . $code . " " . $label, $bodyMessage);
	}

	public static function _400_Bad_Request(string $bodyMessage = null, string $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::INFO) : HttpStatusException {
		return new HttpStatusException("Bad Request", 400, null, $bodyMessage, $debugMessage, $logLevel);
	}

	public static function _401_Unauthorized(string $bodyMessage = null, string $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::WARN) {
		return new HttpStatusException("Unauthorized", 401, null, $bodyMessage, $debugMessage, $logLevel);
	}

	public static function _403_Forbidden(string $bodyMessage = null, string $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::WARN) {
		return new HttpStatusException("Forbidden", 403,  null, $bodyMessage, $debugMessage, $logLevel);
	}

	public static function _404_Not_Found(string $bodyMessage = null, string $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::DEBUG) {
		return new HttpStatusException("Not Found", 404, null, $bodyMessage, $debugMessage, $logLevel);
	}

	public static function _405_Method_Not_Allowed(string $bodyMessage = null, string $debugMessage = null, LOG_LEVEL $logLevel = LOG_LEVEL::WARN) {
		return new HttpStatusException("Method Not Allowed", 405, null, $bodyMessage, $debugMessage, $logLevel);
	}

	public static function _498_Business_Error(IBusinessException $ex, LOG_LEVEL $logLevel = LOG_LEVEL::DEBUG) {
		$msg = $ex->getCode () . " " . $ex->getMessage ();
		return new HttpStatusException("Business Error", 498, $ex, $msg, $msg, $logLevel);
	}

	public static function _499_Already_Authenticated(LOG_LEVEL $logLevel = LOG_LEVEL::DEBUG) {
		return new HttpStatusException("Already Authenticated", 499, null, null, "Already Authenticated", $logLevel);
	}

	public static function _500_Internal_Server_Error(Throwable $ex, string $bodyMessage) {
		return new HttpStatusException("Internal Server Error", 500, $ex, $bodyMessage . ", see server log for more details.", $ex->getMessage(), LOG_LEVEL::FATAL);
	}

	public static function _501_Not_Implemented(string $bodyMessage) {
		return new HttpStatusException("Not Implemented", 501, null, $bodyMessage . ", see server log for more details.", "Not implemented", LOG_LEVEL::FATAL);
	}

}

?>
