<?php
namespace OneMediaPiece_blog\utils;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Compte;
use OneMediaPiece_blog\services\CompteService;
use Exception;
use DateTime;
use Error;
use OneMediaPiece_blog\controller\IController;

// GESTION DES ERREURS
function serverBootstrap()          // désactive l’affichage des erreurs PHP sur la page
{
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_STRICT & ~E_NOTICE & ~E_PARSE);
    ini_set('display_errors', 'off');
}

function headerAndDie ($header)
{
    header($header);
    die();
}

function _400_Bad_Request($msg = "")
{
    headerAndDie("HTTP/1.1 400 Bad Request : " . $msg);
}

function _401_Unauthorized()
{
    headerAndDie("HTTP/1.1 401 Unauthorized : ");
}

function _403_Forbidden($msg = "")
{
    headerAndDie("HTTP/1.1 403 Forbidden : " . $msg);
}

function _404_Not_Found($msg = "")
{
    headerAndDie("HTTP/1.1 404 Not Found : " . $msg);
}

function _405_Method_Not_Allowed($msg = "")
{
    headerAndDie("HTTP/1.1 405 Method Not Allowed : " . $msg);
}

function _499_Authentication_Error($msg = "")
{
    headerAndDie("HTTP/1.1 499 Authentication Error : " . $msg);
}

function _500_Internal_Server_Error($msg = "")
{
    headerAndDie("HTTP/1.1 500 Internal Server Error : " . $msg);
}

function raiseHttpStatus(HttpStatusException $ex) : void
{
    switch($ex->getCode())
    {
        case 400 : _400_Bad_Request($ex->getMessage());
        break;
        case 401 : _401_Unauthorized();
        break;
        case 403 : _403_Forbidden($ex->getMessage());
        break;
        case 404 : _404_Not_Found($ex->getMessage());
        break;
        case 405 : _405_Method_Not_Allowed($ex->getMessage());
        break;
        case 499 : _499_Authentication_Error($ex->getMessage());
        break;
        case 500 : // Ici on veut savoir ce qu'il se passe dans le serveur
            error_log($ex); // c'est un cas d'exception non souhaité...
             _500_Internal_Server_Error($ex->getMessage());
        break;
        default: throw new Exception("Http Status Exception not managed " . $ex->getCode());
    }
}

// CONTROLLERS-ROUTEUR
function extractForm() : array
{
    // test de la méthode, sinon 405
    switch ($_SERVER['REQUEST_METHOD'])
    {
        case 'GET' : return $_GET;
        case 'POST' : return $_POST;
        case 'PUT' : 
            $raw = file_get_contents(('php://input')); // cache php
            $form = [];
            parse_str($raw, $form);     // builtin php d'extraction de formulaire
            return $form;
        case 'DELETE' : return $_GET;
        default : _405_Method_Not_Allowed();
    }
}

function extractRoute( array $FORM) : string
{
    if (!isset($FORM['route']))     // si le paramètre n'existe pas, 400
    {
        _400_Bad_Request("No parameter : route");
    }
    // on extrait la route, puis on sécurise la variable pour éviter les injections
    $ROUTE = $FORM['route'];
    if (preg_match("/^[A-Z][A-Za-z]{1,63}$/", $ROUTE))
    {
        return $ROUTE; // commence par une lettre majuscule, suivi d'une à 63 lettres
    }
    _400_Bad_Request("Wrong syntax : route");
}

// function createController ($FORM, $ROUTE) : IController
// {
//     // Mise en forme de la méthode
//     $METHOD = createMethod();       //Post        

//     // construction du nom de la classe controller
//     $CLASS_NAME = $ROUTE . $METHOD . "Controller";            // ArticleGetController

//     //Construction du chemin absolu
//     $FILE = ROOT . "/controllers/". $CLASS_NAME . ".php";       // racine/controllers/CompteGetController.php

//     // si le fichier n'existe pas, exception
//     if (!file_exists($FILE))
//     {
//         throw new HttpStatusException("Unknow Controller : " . $ROUTE . $METHOD, 404);
//     }
//     try
//     {
//         require($FILE);
//         $CONTROLLER = new $CLASS_NAME($FORM);               // new ArticleGetController($FORM)
//         return $CONTROLLER;
//     }
//     catch(Error $e)
//     {
//         error_log($e);
//         die();                                             // TODO erreur 500
//     }

// }

// CYBERSEC
function createMethod()
{
    $method = strtolower($_SERVER["REQUEST_METHOD"]);           // tout en minuscule
    return ucfirst($method);                                    // 1ere lettre en majuscule
}

//Contrôler si la chaine est un entier naturel [0,N]
function isNaturalInteger (string $str) : bool
{
    return ctype_digit($str);
}

function isWord(string $str, int $start = 1, int $end = 64) : bool {
    return preg_match("/^[A-Za-z]{" .$start . "," .$end . "}$/", $str);
}

// Vérifier si le parametre est conforme (cf. Cybersec)
function isSanitizedString(string $str)
{
    // vérifier si c'est une chaine de caractères
    if (!is_string($str) || (strlen($str) == 0))
    {
        return false;
    } else {
        // vérifier si la chaine est "propre"
        return $str === sanitizeString($str);
    }
}

// Nettoyer le parametre (cf. Cybersec)
function sanitizeString(string $str)
{
    // enlever les balises HTML et PHP
    $str = strip_tags($str);
    // supprimer les espaces (ou d'autres caractères) en début et fin de chaîne
    $str = trim($str);
    // convertir les caractères spéciaux en entités HTML
    $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    return $str;
}

function isPassword(string $password) : bool
{
    $regex = '/^.+$/u'; 
    return preg_match($regex, $password);
}

function hashPassword(string $str) : string {
    return password_hash($str, PASSWORD_BCRYPT);
}

function isEmail(string $login) : bool
{
    $regex = '/^[\w\.-]+@[\w\.-]+\.\w+$/';
    return preg_match($regex, $login);
}

function isDate(?string $date, ?string $format = "MYSQL_DATE_FORMAT") :bool
{
    // TODO codé le isDate
    // trigger_error('TODO : check if date get a correct syntax : in utils/functions.php', E_USER_WARNING);        //Déclenche une erreur utilisateur
    // return true;
    if (!$date) 
    {
        return false; // Si la date est null ou vide, ce n'est pas une date valide
    }

    $d = DateTime::createFromFormat($format, $date);
    // Vérifie si la date est valide et si elle correspond exactement au format
    return $d && $d->format($format) === $date;

}

function isBool(string $bool) : bool 
{
    return $bool === "true" || $bool === "false";
}

function stringToBool(string $value): bool 
{
    return strtolower($value) === 'true'; 
}


//SESSION
//Création de session
function manageSession() : void
{
    session_start();
    initSession();
    if (( $_SESSION[START_TIME] + getMaxTime() ) < time())
    {
        reinitSession();
    }
}

// Initaialise le temps de session
function initSession() : void       // on va créer notre propre timeout de session
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
function isLogged() : bool
{
    return isset(  $_SESSION[COMPTE_ID] );
}

function getCompteIdFromSession() : ?Compte
{
    // return isLogged() ? $_SESSION[COMPTE_ID] : NULL;
    if (isLogged()) {
        $idSession = $_SESSION[COMPTE_ID];
        $idRole = $_SESSION['fk_role'];
        $loginSession = $_SESSION['login'];
        $pseudoSession = $_SESSION['pseudo'];
        $estSupprimeSession = $_SESSION['estSupprime'];
        $estSignaleSession = $_SESSION['estSignale'];
        $estBanniSession = $_SESSION['estBanni'];
        $estEnAttenteDeModerationSession = $_SESSION['enAttenteDeModeration'];
        return Compte::createFromRow((object)[
            'id_compte' => $idSession,
            'fk_role' => $idRole,
            'login' => $loginSession,
            'pseudo' => $pseudoSession,
            'estSupprime' => $estSupprimeSession,
            'estSignale' => $estSignaleSession,
            'estBanni' => $estBanniSession,
            'enAttenteDeModeration' => $estEnAttenteDeModerationSession
        ], false);
    } else {
        return null;
    }
}


// Définir le temps de session maximum
function getMaxTime() : int
{
    return 15 * 60;         // 15 minutes
}

// Redémarrer une nouvelle session
function reinitSession() : void
{
    session_destroy();
    session_start();
    initSession();
}


function login(int $id)
{
    $service = new CompteService();
    $compte = $service->findById($id);

    /** @var Compte $compte */ 
    $_SESSION[COMPTE_ID] = $id;
    $_SESSION['login'] = $compte->getLogin();
    $_SESSION['password'] = $compte->getPassword();
    $_SESSION['pseudo'] = $compte->getPseudo();
    $_SESSION['dateModification'] = $compte->getdateModification();
    $_SESSION['enAttenteDeModeration'] = $compte->getEnAttenteDeModeration();
    $_SESSION['estSupprime'] = $compte->getEstSupprime();
    $_SESSION['estSignale'] = $compte->getEstSignale();
    $_SESSION['estBanni'] = $compte->getEstBanni();
    $_SESSION['roleId'] = $compte->getRole()->getIdRole();
    //var_dump($compte);
}


?>
