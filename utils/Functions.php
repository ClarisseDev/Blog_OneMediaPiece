<?php

	namespace OneMediaPiece_blog\utils;
	use OneMediaPiece_blog\exceptions\HttpStatusException;
	use OneMediaPiece_blog\model\Compte;
	use OneMediaPiece_blog\services\CompteService;
	use DateTime;
	use Exception;

	
	class Functions {

		// REGEX :	Au moins 1 minuscule, Au moins 1 majuscule, Au moins 1 chiffre, Au moins 1 caractère spécial (hors lettre/chiffre/espace), Minimum 8 caractères
		//private const REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/';
		// REGEX qui accepte tous les password
		private const REGEX = '/^.+$/u';
	
		public static function isBool(string $bool) : bool 
		{
			return $bool === "true" || $bool === "false";
		}
		
		public static function isEmail(string $mail) : bool {
		    return filter_var($mail, FILTER_VALIDATE_EMAIL) !== false;
		}
		
		//SESSION
		//Création de session
		public static function manageSession(): void
		{
			if (session_status() === PHP_SESSION_NONE) {
				session_set_cookie_params([
					'lifetime' => 0, // La session expire à la fermeture du navigateur
					'path' => '/',
					'domain' => 'localhost',
					'secure' => false, // Mettez à false si vous n'avez pas de HTTPS
					'httponly' => true,
					'samesite' => 'Lax' // Permet les cookies cross-site
				]);
				session_start();
			}
		}

		// Initaialise le temps de session
		public static function initSession() : void       // on va créer notre propre timeout de session
		{
			if (! isset($_SESSION[START_TIME]))
			{
				$_SESSION[START_TIME] = time();
			}
			else if (isLogged())             // l'utilisateur est logué, on rajoute du temps
			{
				$_SESSION[START_TIME] = time();
			}
		}

		// Vérifier si l'utilisateur est logué
		public static function isLogged(): bool {
			return isset($_SESSION[COMPTE_ID]);
		}

		public static function checkPassword(string $password) : bool {
			// return true; // TODO find some code to test password complexicity (Open Source) 
			return preg_match(Functions::REGEX, $password);
		}

		public static function isPassword(string $password) : bool
		{
			$regex = '/^.+$/u'; 
			return preg_match($regex, $password);
		}
	
		public static function hashPassword(string $str) : string {
			return password_hash($str, PASSWORD_BCRYPT);
		}

		/**
		 * Each element of the specified array is copied into a new array.
		 * Each element is wrapped between prefix et suffix.
		 * @param array $array the source array
		 * @param string $prefix the prefix
		 * @param string $suffix the suffix
		 * @return array the new array of wrapped elements
		 */
		public static function wrap(array $array, string $prefix, string $suffix) : array {
			$newArray = array_map(function($item) use ($prefix, $suffix) {
				return $prefix . $item . $suffix;
			}, $array);
			return $newArray;
		}
		
		/**
		 * Controlle si la chaine est un entier naturel [0,N]
		 * @param string $str
		 * @return bool
		 */
		public static function isNaturalInteger(string $str) : bool {
			return ctype_digit( $str ); 
		}
		
		public static function isWord(string $str, int $start = 1, int $end = 64) : bool {
			return preg_match("/^[A-Za-z]{" .$start . "," .$end . "}$/", $str);
		}
		
		public static function isSanitizedString(string $str) : bool {
		    return $str == self::sanitizeString($str);
		}

		public static function isSanitizedContent(string $str): bool
		{
			// Vérifie qu'il n'y a pas de balises HTML ou de scripts
			if (strip_tags($str) !== $str) {
				return false;
			}

			// Vérifie qu'il n'y a pas de caractères interdits (comme <, >, etc.)
			// Autorise les caractères spéciaux et les accents
			return !preg_match('/[<>]/', $str);
		}

		public static function stringToBool(string $value): bool 
		{
			return strtolower($value) === 'true'; 
		}
		
		public static function checkFormElement(array $form, string $name) {
			if (! isset($form[$name])) { // l'id doit etre présent
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Missing " . $name, debugMessage: "CYBERSEC Receive bad request, no parameter : " . $name);
			}
		}
		
		public static function getWord(array $form, string $name) : int {
			if (! self::isWord($form[$name])) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a word : " . $name);
			}
			return $form[$name];
		}
		
		public static function getDateFromClient(array $form, string $name) : DateTime {
			try {
				return new DateTime($form[$name]);
			} catch (Exception $ex) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a date : " . $name);
			}
		}
		
		public static function getBoolean(array $form, string $name) : bool {
			if (! self::isBool($form[$name])) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a boolean : " . $name);
			}
			return $form[$name] == "true";
		}
		
		public static function getNaturalInteger(array $form, string $name) : int {
			if (! self::isNaturalInteger($form[$name])) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not an integer : " . $name);
			}
			return intval($form[$name]);
		}
		
		public static function getEmail(array $form, string $name) : string {
			if (! self::isEmail( $form[$name])) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not an email : " . $name);
			}
			return $form[$name];
		}
		
		public static function getPassword(array $form, string $name) : string {
			if (! self::checkPassword( $form[$name] )) { // Complexicité pour check sqli / xss / csrf
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a paswword : " . $name);
			}
			return $form[$name];
		}
		
		public static function getLogin(array $form, string $name) : string {
			if (! self::isLogged( $form[$name] )) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a login : " . $name);
			}
			return $form[$name];
		}
		
		public static function getSanitizedString(array $form, string $name) : string {
			if (! self::isSanitizedString( $form[$name] )) {
				throw HttpStatusException::_400_Bad_Request(bodyMessage: "Wrong " . $name, debugMessage: "CYBERSEC Receive bad request, param is not a sanitized string : " . $name);
			}
			return $form[$name];
		}
		
		public static function sanitizeString(string $input) : string {
			return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); // Convertit les caractères spéciaux en entités HTML pour éviter les injections XSS
		}
		
	}
?>