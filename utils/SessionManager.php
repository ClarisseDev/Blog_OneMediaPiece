<?php
namespace OneMediaPiece_blog\utils;

use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\services\CompteService;
use OneMediaPiece_blog\exceptions\HttpStatusException;

class SessionManager {
    private static ?SessionManager $_INSTANCE = null;

    const START_TIME = "start_time";
    const COMPTE_ID = "compte_id";
    const ROLE_ID = "roleId";

    private function __construct() {}

    public static function getInstance(): SessionManager {
        if (is_null(self::$_INSTANCE)) {
            self::$_INSTANCE = new SessionManager();
        }
        return self::$_INSTANCE;
    }

    function getStartTime() {
        return $_SESSION[self::START_TIME];
    }

    function getTimeOut(): int {
        return $_SESSION[self::START_TIME] + $this->getMaxTime();
    }

	public static function manageSession(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_set_cookie_params([
				'lifetime' => 0,            // signifie que le cookie expire à la fin de la session du navigateur
				'path' => '/',              // signifie que le cookie est valide pour tout le site
				'domain' => 'localhost',    // limite le cookie au domaine local //TODO à changer en prod
				'secure' => false,          // signifie que le cookie peut être envoyé en HTTP (non sécurisé) //TODO à changer en prod 
				'httponly' => true,         // empêche l'accès au cookie via JavaScript (sécurité contre les attaques XSS)
				'samesite' => 'Lax'         // limite l'envoi du cookie aux requêtes "sûres" (exclut les requêtes cross-site non sécurisées)
			]);
			session_start();
		}
		error_log("Session ID: " . session_id());
		error_log("Session: " . print_r($_SESSION, true));
		self::initSession();
        if ( ($_SESSION[self::START_TIME] + self::getMaxTime()) < time() ) {
				self::reinitSession();
			}
		if (self::isSessionExpired()) {
			self::reinitSession();
		}
	}

    public static function initSession(): void {
        if (!isset($_SESSION[self::START_TIME])) {
            $_SESSION[self::START_TIME] = time();
        } else if (self::isLogged()) {
            $_SESSION[self::START_TIME] = time();
        }
    }

    public static function isSessionExpired(): bool {
        return isset($_SESSION[self::START_TIME]) && ($_SESSION[self::START_TIME] + self::getMaxTime()) < time();
    }

    // Redémarrer une nouvelle session
    public static function reinitSession(): void {
        error_log("reinitSession() - Destruction de la session...");
        session_destroy();
        session_start();
        self::initSession();
    }

    public static function isLogged(): bool {
        return isset($_SESSION[self::START_TIME]) && isset($_SESSION[self::COMPTE_ID]);
    }

    public static function getCompteIdFromSession(): ?int {
        if (!self::isLogged() || !isset($_SESSION[self::COMPTE_ID])) {
            return null;
        }
        $id = $_SESSION[self::COMPTE_ID];
        return filter_var($id, FILTER_VALIDATE_INT) ? $id : null;
    }

	public static function getCompteFromSession(): ?Compte
	{
		if (!self::isLogged()) {
			return null;
		}

		$compteService = new CompteService();
		$compteId = self::getCompteIdFromSession();
		return $compteService->findByIdForSession($compteId);
	}

    public static function getRoleIdFromSession(): ?int {
        return self::isLogged() ? $_SESSION[self::ROLE_ID] : null;
    }

    public static function getMaxTime(): int {
        return 60 * 60; // 60 minutes
    }

    public static function login(int $id): void {
        $compteService = new CompteService();
        $compte = $compteService->findById($id);

		/**
		* @var Compte $compte
		*/
        $_SESSION[self::COMPTE_ID] = $id;
        $_SESSION[self::ROLE_ID] = $compte->getRole()->getIdRole();
        session_regenerate_id(true);
    }

    public static function logout(): void {
        self::reinitSession();
    }
}
?>
